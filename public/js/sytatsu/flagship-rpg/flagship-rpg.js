/*
 * Flagship RPG: countdowns and the fight replay. Plain Alpine data, no build step.
 */
(() => {
    const pad = (n) => String(n).padStart(2, '0');

    const clock = (s) => {
        const h = Math.floor(s / 3600);
        const m = Math.floor((s % 3600) / 60);
        const x = s % 60;

        return h ? `${h}:${pad(m)}:${pad(x)}` : `${m}:${pad(x)}`;
    };

    // A timer that counts down locally between server refreshes. At zero it asks the
    // screen (and through it the sidebar) to refresh, so the result shows up right away.
    window.rpgCountdown = (seconds, total = 0, endsAt = null) => ({
        left: seconds,
        total,
        end: 0,
        timer: null,

        init() {
            this.end = endsAt ?? Date.now() / 1000 + seconds;
            this.timer = setInterval(() => this.tick(), 1000);
            this.tick();
        },

        destroy() {
            clearInterval(this.timer);
            clearTimeout(this.timer);
        },

        tick() {
            this.left = Math.max(0, Math.round(this.end - Date.now() / 1000));

            if (this.left <= 0) {
                clearInterval(this.timer);
                this.refreshUntilDone();
            }
        },

        // The browser clock can run a little ahead of the server's: ask again every few
        // seconds until the server has finished the work and replaced this timer.
        refreshUntilDone(tries = 0) {
            if (tries > 6 || !this.$el.isConnected) return;

            this.timer = setTimeout(() => {
                window.Livewire.dispatch('rpg-refresh');
                this.refreshUntilDone(tries + 1);
            }, tries === 0 ? 1200 : 4000);
        },

        get label() {
            return this.left > 0 ? clock(this.left) : 'Done';
        },

        get percent() {
            return this.total ? Math.min(100, ((this.total - this.left) / this.total) * 100) : 0;
        },
    });

    // Fitting: drag and drop modules between the hold and the hull's sockets, or tap a
    // module and then a socket. Every move is checked again on the server. With fit off
    // (the shipyard) a tap only selects a module.
    window.rpgFitting = (fit = true) => ({
        fit,
        hangar: false,
        selected: null,
        selectedSlot: null,
        selectedFitted: false,
        selectedBlocked: false,
        drag: null,
        overHold: false,
        filter: 'all',

        pick(id, slot, fitted, blocked) {
            if (this.selected === id) {
                this.selected = null;
                return;
            }

            this.selected = id;
            this.selectedSlot = slot;
            this.selectedFitted = fitted;
            this.selectedBlocked = blocked;
        },

        start(event, id, slot, fitted, blocked) {
            if (blocked) {
                event.preventDefault();
                return;
            }

            this.drag = { id, slot, fitted };
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', String(id));
        },

        end() {
            this.drag = null;
            this.overHold = false;
        },

        // The module being moved: the one dragged, or a selected one from the hold.
        moving() {
            if (this.drag) return this.drag;
            if (this.selected && !this.selectedFitted && !this.selectedBlocked) return { id: this.selected, slot: this.selectedSlot, fitted: false };

            return null;
        },

        canTake(slot) {
            if (!this.fit) return false;
            const m = this.moving();

            return !!m && m.slot === slot;
        },

        drop(slot, index) {
            const m = this.drag;
            this.end();

            if (m && m.slot === slot) {
                this.$wire.equip(m.id, index);
                this.selected = m.id;
                this.selectedSlot = slot;
                this.selectedFitted = true;
            }
        },

        socketClick(slot, index, id) {
            const m = this.moving();

            if (this.fit && m && !m.fitted && m.slot === slot) {
                this.$wire.equip(m.id, index);
                this.selectedFitted = true;
                return;
            }

            if (id) {
                this.pick(id, slot, true, false);
            } else {
                this.selected = null;
            }
        },

        dropHold() {
            const m = this.drag;
            this.end();

            if (m && m.fitted) {
                this.$wire.unequip(m.id);
                this.selectedFitted = false;
            }
        },
    });

    // Plays a stored fight turn by turn (about half a second per turn).
    window.rpgReplay = (fight) => ({
        fight,
        index: 0,
        hpA: fight.a.hp,
        hpB: fight.b.hp,
        lines: [],
        hit: null,
        finished: false,
        timer: null,

        init() {
            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            this.timer = setInterval(() => this.step(), reduced ? 250 : 500);
        },

        destroy() {
            clearInterval(this.timer);
        },

        step() {
            const turn = this.fight.turns[this.index];

            if (!turn) {
                this.finish();
                return;
            }

            this.hpA = turn.hp_a;
            this.hpB = turn.hp_b;
            this.hit = turn.evaded ? null : (turn.attacker === 'a' ? 'b' : 'a');
            this.lines.unshift({ id: this.index, ...this.describe(turn) });
            this.index++;

            if (this.index >= this.fight.turns.length) {
                this.finish();
            }
        },

        skip() {
            while (!this.finished) {
                this.step();
            }
        },

        finish() {
            clearInterval(this.timer);
            this.finished = true;
            this.hit = null;
        },

        describe(t) {
            const attacker = t.attacker === 'a' ? this.fight.a.name : this.fight.b.name;
            const defender = t.attacker === 'a' ? this.fight.b.name : this.fight.a.name;
            const notes = t.notes || [];
            const tags = [];

            if (notes.includes('overcharge')) tags.push('Overcharge');
            if (notes.includes('momentum')) tags.push('Momentum');
            if (notes.includes('last_stand')) tags.push('Last Stand');

            if (t.evaded) {
                return {
                    side: t.attacker,
                    kind: 'miss',
                    text: notes.includes('phase_shield')
                        ? `${defender}'s phase shield swallows the first shot`
                        : `${attacker} fires, ${defender} dodges`,
                    tags,
                };
            }

            let text = `${attacker} hits for ${t.damage}`;
            if (t.absorbed >= 1) text += ` (${Math.round(t.absorbed)} absorbed by shields)`;

            return { side: t.attacker, kind: t.crit ? 'crit' : 'hit', text: (t.crit ? 'Critical! ' : '') + text, tags };
        },

        percent(hp, max) {
            return Math.max(0, Math.min(100, (hp / max) * 100));
        },
    });
})();
