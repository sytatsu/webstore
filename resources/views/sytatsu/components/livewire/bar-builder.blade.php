<div
    x-data="barBuilder(@js($catalog))"
    x-init="init()"
    @bar-builder-added.window="flash('{{ __('Added to cart') }}' + ' &middot; ' + $event.detail.reference)"
    @bar-builder-error.window="flash($event.detail.message)"
    class="rounded-2xl bg-white dark:bg-slate-800 shadow-md dark:shadow-slate-700"
>
    <div class="flex flex-col lg:grid lg:grid-cols-[minmax(0,1fr)_408px] lg:items-start gap-6 p-6 md:p-8">

        {{-- ── stage: its own section, sticky alongside the panel on large screens only ── --}}
        <section class="rounded-2xl overflow-hidden ring-1 ring-black/10 z-20 lg:sticky lg:top-4 lg:self-start"
                 style="background: radial-gradient(120% 90% at 50% 8%, #32363D 0%, #1B1D21 62%, #141619 100%);">
            <div class="px-5 pt-4 flex items-center justify-between">
                <span class="font-mono text-[10px] tracking-[.16em] uppercase text-white/40">{{ __('Live preview') }}</span>
                <span class="font-mono text-[11px] text-white/40" x-text="hasSelection ? '{{ __('Click a cap to edit it') }}' : '{{ __('No cap selected') }}'"></span>
            </div>

            <div class="px-4 sm:px-8 py-10 sm:py-14 select-none" @click="onStageClick($event)" x-html="svg"></div>

            <div class="px-5 pb-5 flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-white/[.07] pt-4">
                <div class="font-mono text-[11px] text-white/50">
                    <span x-show="hasSelection">{{ __('Cap') }} <span class="text-white" x-text="selected + 1"></span>/<span x-text="caps.length"></span></span>
                    <span x-show="!hasSelection" class="text-white/70">{{ __('Preview mode') }}</span>
                </div>
                <template x-if="hasSelection">
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                        <div class="font-mono text-[11px] text-white/50">
                            <span x-show="!caps[selected]?.icon">{{ __('Char') }} <span class="text-white" x-text="caps[selected]?.char?.trim() || '—'"></span></span>
                            <span x-show="caps[selected]?.icon">{{ __('Icon') }} <span class="text-white" x-text="caps[selected]?.icon?.name"></span></span>
                        </div>
                        <div class="font-mono text-[11px] text-white/50 flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-3.5 h-3.5 rounded-[3px] ring-1 ring-white/25 text-[9px] font-bold"
                                  :style="`background:${caps[selected]?.hex};color:${caps[selected]?.textHex}`">A</span>
                            <span class="text-white" x-text="comboName(caps[selected])"></span>
                        </div>
                    </div>
                </template>
                <div class="font-mono text-[11px] text-white/50 flex items-center gap-2">
                    {{ __('Base') }}
                    <span class="inline-block w-2.5 h-2.5 rounded-full ring-1 ring-white/25" :style="`background:${baseHex}`"></span>
                    <span class="text-white" x-text="baseColorName(baseHex)"></span>
                </div>
                <div class="ml-auto flex items-center gap-4">
                    <button x-show="hasSelection" type="button"
                            class="font-mono text-[11px] text-white/50 hover:text-white underline underline-offset-4"
                            @click="deselect()">{{ __('Deselect') }}</button>
                </div>
            </div>
        </section>

        {{-- ── control panel ───────────────────────────────────────────── --}}
        <section class="rounded-2xl ring-1 ring-gray-200 dark:ring-gray-600 divide-y divide-gray-200 dark:divide-gray-600 text-black dark:text-white">

            {{-- 01 text --}}
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-mono text-[10px] tracking-[.16em] uppercase text-gray-500 dark:text-gray-400">01 &mdash; {{ __('Text') }}</h2>
                    <div class="flex items-center gap-1">
                        <button type="button" class="size-7 inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-500 disabled:opacity-30"
                                @click="removeCap()" :disabled="caps.length <= MIN" aria-label="{{ __('Remove a cap') }}">&minus;</button>
                        <span class="font-mono text-[12px] w-16 text-center" x-text="caps.length + ' {{ __('caps') }}'"></span>
                        <button type="button" class="size-7 inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-500 disabled:opacity-30"
                                @click="addCap()" :disabled="caps.length >= MAX" aria-label="{{ __('Add a cap') }}">+</button>
                    </div>
                </div>

                <label class="sr-only" for="bar-builder-word">{{ __('Bar text') }}</label>
                <div class="relative">
                    <input id="bar-builder-word"
                           class="w-full border border-gray-300 dark:border-gray-500 rounded-lg bg-white dark:bg-slate-900 px-3.5 py-3 font-mono text-lg tracking-[.22em] uppercase text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-secondary"
                           :maxlength="MAX" autocomplete="off" spellcheck="false"
                           :value="word"
                           :placeholder="caps.some(c => c.icon) ? '' : '{{ __('YOUR WORD') }}'"
                           @input="setWord($event.target.value); syncSelectionFromInput($event.target)"
                           @click="syncSelectionFromInput($event.target)"
                           @keyup="syncSelectionFromInput($event.target)"
                           @focus="syncSelectionFromInput($event.target)">
                    <template x-for="item in iconOverlays" :key="'icon-ovl-' + item.i">
                        <svg viewBox="0 0 100 100" width="18" height="18"
                             class="absolute top-1/2 -translate-y-1/2 text-black dark:text-white pointer-events-none"
                             :style="`left: ${item.left}px`"
                             aria-hidden="true">
                            <g :transform="`translate(${(50 - item.icon.scale * item.icon.cx).toFixed(2)} ${(50 - item.icon.scale * item.icon.cy).toFixed(2)}) scale(${item.icon.scale})`">
                                <path :d="item.icon.path" fill="currentColor"></path>
                            </g>
                        </svg>
                    </template>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Letters, digits and symbols.') }} <span x-text="MIN"></span>&ndash;<span x-text="MAX"></span> {{ __('caps. Select a cap and type to change one at a time.') }}
                </p>

                @if(count($catalog['icons']))
                    <div class="mt-4" :class="!hasSelection && 'opacity-40 pointer-events-none'">
                        <div class="font-mono text-[10px] tracking-[.16em] uppercase text-gray-500 dark:text-gray-400 mb-2">
                            <span x-show="hasSelection">{{ __('Icons') }} &mdash; {{ __('applied to cap') }} <span x-text="selected + 1"></span></span>
                            <span x-show="!hasSelection">{{ __('Icons') }} &mdash; {{ __('select a cap first') }}</span>
                        </div>
                        <div class="grid grid-cols-10 gap-1.5">
                            <template x-for="ic in ICONS" :key="ic.id">
                                <div class="relative group">
                                    <button type="button"
                                            class="aspect-square w-full grid place-items-center rounded-lg bg-white dark:bg-slate-900 border border-gray-300 dark:border-gray-600 text-black dark:text-white hover:border-black dark:hover:border-white"
                                            :disabled="!hasSelection" :aria-label="ic.name"
                                            @click="chooseIcon(ic)">
                                        <svg viewBox="0 0 100 100" width="20" height="20" aria-hidden="true">
                                            <g :transform="`translate(${(50 - ic.scale * ic.cx).toFixed(2)} ${(50 - ic.scale * ic.cy).toFixed(2)}) scale(${ic.scale})`">
                                                <path :d="ic.path" fill="currentColor"></path>
                                            </g>
                                        </svg>
                                    </button>
                                    <span class="pointer-events-none absolute left-1/2 bottom-full -translate-x-1/2 mb-1.5 whitespace-nowrap rounded-md bg-black dark:bg-white px-2 py-1 font-mono text-[10px] text-white dark:text-black opacity-0 group-hover:opacity-100 transition-opacity z-10"
                                          x-text="ic.name"></span>
                                </div>
                            </template>
                            <div class="relative group">
                                <button type="button"
                                        class="aspect-square w-full grid place-items-center rounded-lg bg-white dark:bg-slate-900 border border-gray-300 dark:border-gray-600 hover:border-black dark:hover:border-white"
                                        :disabled="!hasSelection" aria-label="{{ __('Blank') }}"
                                        @click="setChar(selected, ' ')">
                                    <span class="block w-3.5 h-3.5 rounded-[3px] border border-dashed border-gray-400 dark:border-gray-500"></span>
                                </button>
                                <span class="pointer-events-none absolute left-1/2 bottom-full -translate-x-1/2 mb-1.5 whitespace-nowrap rounded-md bg-black dark:bg-white px-2 py-1 font-mono text-[10px] text-white dark:text-black opacity-0 group-hover:opacity-100 transition-opacity z-10">{{ __('Blank') }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 02 colour combination --}}
            <div class="p-5">
                <div class="flex items-baseline justify-between mb-3">
                    <h2 class="font-mono text-[10px] tracking-[.16em] uppercase text-gray-500 dark:text-gray-400">02 &mdash; {{ __('Colour combination') }}</h2>
                    <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="hasSelection ? comboName(caps[selected]) : '{{ __('select a cap') }}'"></span>
                </div>

                <div class="grid grid-cols-8 gap-2.5" :class="!hasSelection && 'opacity-40 pointer-events-none'">
                    <template x-for="c in CAP_COMBOS" :key="c.id">
                        <button type="button"
                                class="relative aspect-square rounded-[10px] grid place-items-center font-bold text-lg avenir-bold"
                                :style="`background:${c.cap};color:${c.text};box-shadow:inset 0 0 0 1px rgba(0,0,0,.16), 0 1px 2px rgba(0,0,0,.10)`"
                                :class="!isAvail(c) && 'opacity-40 grayscale cursor-not-allowed'"
                                :title="isAvail(c) ? c.name : c.name + ' &mdash; {{ __('unavailable') }}'"
                                :disabled="!hasSelection || !isAvail(c)"
                                :aria-pressed="isCombo(caps[selected], c).toString()"
                                @click="setCombo(selected, c)">A
                            <span x-show="isCombo(caps[selected], c)" class="absolute -inset-1 rounded-[13px] ring-2 ring-secondary"></span>
                            <span x-show="!isAvail(c)" class="absolute inset-0 rounded-[inherit] pointer-events-none"
                                  style="background: linear-gradient(to top right, transparent calc(50% - 1.1px), rgba(0,0,0,.6) calc(50% - 1.1px), rgba(0,0,0,.6) calc(50% + 1.1px), transparent calc(50% + 1.1px));"></span>
                        </button>
                    </template>
                </div>

                <div class="mt-3">
                    <button type="button" class="text-xs font-medium border border-gray-300 dark:border-gray-500 rounded-lg px-3 py-1.5 disabled:opacity-30"
                            @click="applyToAll()" :disabled="!hasSelection">{{ __('Apply to all caps') }}</button>
                </div>
            </div>

            {{-- 03 base colour --}}
            <div class="p-5">
                <div class="flex items-baseline justify-between mb-3">
                    <h2 class="font-mono text-[10px] tracking-[.16em] uppercase text-gray-500 dark:text-gray-400">03 &mdash; {{ __('Base colour') }}</h2>
                    <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="baseColorName(baseHex)"></span>
                </div>
                <div class="grid grid-cols-8 gap-2.5">
                    <template x-for="c in BASE_COLORS" :key="c.hex">
                        <button type="button"
                                class="relative aspect-square rounded-full"
                                :style="`background:${c.hex};box-shadow:inset 0 0 0 1px rgba(0,0,0,.14), 0 1px 2px rgba(0,0,0,.10)`"
                                :class="!isAvail(c) && 'opacity-40 grayscale cursor-not-allowed'"
                                :title="isAvail(c) ? c.name : c.name + ' &mdash; {{ __('unavailable') }}'"
                                :disabled="!isAvail(c)"
                                :aria-pressed="(baseHex === c.hex).toString()"
                                @click="setBaseColor(c.hex)">
                            <span x-show="baseHex === c.hex" class="absolute -inset-1 rounded-full ring-2 ring-secondary"></span>
                            <span x-show="!isAvail(c)" class="absolute inset-0 rounded-[inherit] pointer-events-none"
                                  style="background: linear-gradient(to top right, transparent calc(50% - 1.1px), rgba(0,0,0,.6) calc(50% - 1.1px), rgba(0,0,0,.6) calc(50% + 1.1px), transparent calc(50% + 1.1px));"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- spec + cart --}}
            <div class="p-5 bg-gray-50 dark:bg-slate-900 rounded-b-2xl">
                <div class="font-mono text-[11.5px] text-gray-500 dark:text-gray-400 space-y-1 mb-4">
                    <div class="flex justify-between"><span x-text="caps.length + ' {{ __('caps') }}'"></span><span x-text="currentPrice"></span></div>
                    <div class="pt-1 text-[10.5px]">{{ __('Ref') }} <span x-text="reference"></span></div>
                </div>

                <button type="button"
                        class="w-full rounded-xl bg-black dark:bg-white text-white dark:text-black font-display avenir-bold tracking-wide py-3.5 text-sm hover:bg-secondary dark:hover:bg-secondary dark:hover:text-white transition-colors"
                        @click="addToCart()" wire:loading.attr="disabled" wire:target="addToCart">
                    {{ __('ADD TO CART') }} &mdash; <span x-text="currentPrice"></span>
                </button>

                @if(config('app.debug'))
                    <button type="button" class="mt-2 w-full text-[11px] text-gray-500 dark:text-gray-400 font-mono hover:text-black dark:hover:text-white underline underline-offset-4"
                            @click="showPayload = !showPayload"
                            x-text="showPayload ? '{{ __('Hide configuration payload') }}' : '{{ __('Show configuration payload') }}'"></button>

                    <pre x-show="showPayload" x-cloak
                         class="mt-3 p-3 rounded-lg bg-black text-white/80 font-mono text-[10.5px] leading-relaxed overflow-x-auto"
                         x-text="JSON.stringify(payload, null, 2)"></pre>
                @endif
            </div>
        </section>
    </div>

    {{-- toast --}}
    <div x-show="toast" x-transition x-cloak
         class="fixed bottom-5 left-1/2 -translate-x-1/2 bg-black text-white text-[13px] px-4 py-2.5 rounded-full shadow-lg font-mono z-50"
         x-html="toast"></div>
</div>

@script
<script>
    Alpine.data('barBuilder', (catalog) => ({
        MIN: catalog.minCaps,
        MAX: catalog.maxCaps,
        CAPS_CATALOG: catalog.caps,
        BASE_COLORS: catalog.baseColors,
        CAP_COMBOS: catalog.capCombos,
        ICONS: catalog.icons,
        DEFAULTS: catalog.defaults,
        DRAFT: catalog.draft,

        word: '',
        caps: [],
        baseHex: catalog.defaults?.baseColorHex ?? (catalog.baseColors.find(c => c.available)?.hex ?? catalog.baseColors[0]?.hex),
        selected: 0,
        pressed: null,
        showPayload: false,
        toast: '',

        init() {
            if (window.barBuilderHydrated) return;
            window.barBuilderHydrated = true;

            if (this.DRAFT) {
                this.loadFromMeta(this.DRAFT);
                return;
            }
            this.initFromDefaults();
        },
        initFromDefaults() {
            const defaults = this.DEFAULTS || {};
            const word = (defaults.word || 'CLICKERZ').toUpperCase().slice(0, this.MAX);
            // A trailing icon cap has no character, so defaults.caps can be
            // longer than the (trimmed) word — don't lose those positions.
            const length = Math.max(word.length, defaults.caps?.length ?? 0, this.MIN);
            const chars = [...word];
            while (chars.length < length) chars.push(' ');

            this.caps = chars.map((ch, i) => {
                const def = defaults.caps?.[i];
                const combo = def ? {cap: def.cap, text: def.text} : pick(this.availableCombos);
                const icon = def?.icon ?? null;
                return {char: icon ? ' ' : ch, hex: combo.cap, textHex: combo.text, icon};
            });

            this.word = this.caps.map(c => c.char).join('').trimEnd();
            this.selected = Math.min(this.selected, this.caps.length - 1);
        },
        // Restores a design previously saved via queueDraftSave(), which takes
        // priority over the admin-configured defaults whenever both exist.
        loadFromMeta(meta) {
            const word = (meta.text || '').toUpperCase().slice(0, this.MAX);
            // A trailing icon cap has no character, so meta.caps can be
            // longer than the (trimmed) word — don't lose those positions.
            const length = Math.max(word.length, meta.caps?.length ?? 0, this.MIN);
            const chars = [...word];
            while (chars.length < length) chars.push(' ');

            this.caps = chars.map((ch, i) => {
                const capMeta = meta.caps?.[i];
                const fallback = pick(this.availableCombos);
                const icon = capMeta?.icon ? (this.ICONS.find(ic => ic.id === capMeta.icon.id) ?? null) : null;

                return {
                    char: icon ? ' ' : ch,
                    hex: capMeta?.colour?.hex ?? fallback.cap,
                    textHex: capMeta?.text_colour?.hex ?? fallback.text,
                    icon,
                };
            });

            this.word = this.caps.map(c => c.char).join('').trimEnd();
            this.baseHex = meta.base_colour?.hex ?? this.baseHex;
            this.selected = Math.min(this.selected, this.caps.length - 1);
        },
        queueDraftSave() {
            clearTimeout(this._draftSaveTimer);
            this._draftSaveTimer = setTimeout(() => {
                this.$wire.saveDraft(this.payload.meta);
            }, 1800);
        },

        get availableCombos() {
            return this.CAP_COMBOS.filter(c => this.isAvail(c));
        },

        get svg() {
            const n = this.caps.length;
            const CELL = 96, PITCH = 89, H = 96, R = 26;
            const FIL = 7;
            const W = CELL + (n - 1) * PITCH;
            const b = this.baseHex;

            const rr = (cx, cy, s, rad, attrs) =>
                `<rect x="${cx - s / 2}" y="${cy - s / 2}" width="${s}" height="${s}" rx="${rad}" ${attrs}/>`;

            const outline = (h) => {
                const d = (PITCH - CELL) / 2 + R;
                const L = R + FIL;
                const fy = R - Math.sqrt(L * L - d * d);
                const tx = d * (1 - R / L);
                const ty = R * (fy + FIL) / L;
                let p = `M ${R} 0`;
                for (let i = 0; i < n - 1; i++) {
                    const xa = i * PITCH + CELL - R, xb = (i + 1) * PITCH + R;
                    const mid = (xa + xb) / 2;
                    p += ` L ${xa} 0`
                        + ` A ${R} ${R} 0 0 1 ${(mid - tx).toFixed(2)} ${ty.toFixed(2)}`
                        + ` A ${FIL} ${FIL} 0 0 0 ${(mid + tx).toFixed(2)} ${ty.toFixed(2)}`
                        + ` A ${R} ${R} 0 0 1 ${xb} 0`;
                }
                p += ` L ${W - R} 0 Q ${W} 0 ${W} ${R} L ${W} ${h - R} Q ${W} ${h} ${W - R} ${h}`;
                for (let i = n - 2; i >= 0; i--) {
                    const xa = i * PITCH + CELL - R, xb = (i + 1) * PITCH + R;
                    const mid = (xa + xb) / 2;
                    p += ` L ${xb} ${h}`
                        + ` A ${R} ${R} 0 0 1 ${(mid + tx).toFixed(2)} ${(h - ty).toFixed(2)}`
                        + ` A ${FIL} ${FIL} 0 0 0 ${(mid - tx).toFixed(2)} ${(h - ty).toFixed(2)}`
                        + ` A ${R} ${R} 0 0 1 ${xa} ${h}`;
                }
                p += ` L ${R} ${h} Q 0 ${h} 0 ${h - R} L 0 ${R} Q 0 0 ${R} 0 Z`;
                return p;
            };

            const body = `
        <path d="${outline(H + 5)}" fill="${shade(b, -0.30)}"/>
        <path d="${outline(H)}" fill="${b}"/>
        <path d="${outline(H)}" fill="none" stroke="${shade(b, 0.16)}" stroke-width="1.5" opacity=".55"/>`;

            let defs = '';
            const caps = this.caps.map((c, i) => {
                const cx = CELL / 2 + i * PITCH, cy = H / 2;
                const down = i === this.pressed;
                const dy = down ? 3 : 0;

                defs += `
          <linearGradient id="side${i}" x1="0" x2="0" y1="0" y2="1">
            <stop offset="0"   stop-color="${shade(c.hex, 0.30)}"/>
            <stop offset=".12" stop-color="${shade(c.hex, 0.06)}"/>
            <stop offset=".5"  stop-color="${shade(c.hex, -0.20)}"/>
            <stop offset=".8"  stop-color="${shade(c.hex, -0.38)}"/>
            <stop offset="1"   stop-color="${shade(c.hex, -0.55)}"/>
          </linearGradient>
          <linearGradient id="face${i}" x1="0" x2="0" y1="0" y2="1">
            <stop offset="0"   stop-color="${shade(c.hex, 0.13)}"/>
            <stop offset=".5"  stop-color="${c.hex}"/>
            <stop offset="1"   stop-color="${shade(c.hex, -0.12)}"/>
          </linearGradient>`;

                return `
        <g class="cap-g" data-cap="${i}" transform="translate(0 ${dy})" style="cursor:pointer">
          ${rr(cx, cy + 3 - dy, 82, 19, `fill="rgba(0,0,0,.28)"`)}
          ${rr(cx, cy, 82, 19, `fill="url(#side${i})"`)}
          ${rr(cx, cy + (down ? 1.5 : 2.5), 72, 15.5, `fill="rgba(0,0,0,.16)"`)}
          ${rr(cx, cy - 2, 70, 14.5, `fill="url(#face${i})"`)}
          <ellipse cx="${cx}" cy="${cy - 20}" rx="26" ry="9" fill="#fff" opacity="${isDark(c.hex) ? .16 : .40}"/>
          ${c.icon
                    ? `<g transform="translate(${(cx - c.icon.scale * c.icon.cx).toFixed(2)} ${((cy - 2) - c.icon.scale * c.icon.cy).toFixed(2)}) scale(${c.icon.scale})">
                 <path d="${c.icon.path}" fill="${c.textHex}" opacity=".95"/>
               </g>`
                    : `<text x="${cx}" y="${cy - 2 + (c.char && c.char.length ? 62 * 0.354 : 0)}" text-anchor="middle"
                     font-family="'AvenirNextLTPro-Bold','IBM Plex Sans','Segoe UI Symbol',sans-serif" font-weight="700"
                     font-size="${c.char && c.char.length ? 62 : 0}" fill="${c.textHex}"
                     opacity=".95">${esc(c.char)}</text>`}
        </g>`;
            }).join('');

            const selRing = this.hasSelection
                ? rr(CELL / 2 + this.selected * PITCH, H / 2, 88, 21.5,
                    `fill="none" stroke="${ringFor(b)}" stroke-width="2.5" opacity=".95" pointer-events="none"`)
                : '';

            return `<svg viewBox="-14 -16 ${W + 28} ${H + 42}" width="100%"
                   style="max-height:240px;display:block;overflow:visible"
                   role="img" aria-label="{{ __('Preview of the clicker bar') }}">
                <defs>${defs}</defs>${body}${caps}${selRing}
              </svg>`;
        },

        get iconOverlays() {
            const inputEl = document.getElementById('bar-builder-word');
            if (!inputEl) return [];

            const style = getComputedStyle(inputEl);

            if (!this._measureCanvas) this._measureCanvas = document.createElement('canvas');
            const ctx = this._measureCanvas.getContext('2d');
            ctx.font = `${style.fontWeight} ${style.fontSize} ${style.fontFamily}`;
            if ('letterSpacing' in ctx) ctx.letterSpacing = style.letterSpacing;

            const paddingLeft = parseFloat(style.paddingLeft) || 0;
            let acc = '';
            const overlays = [];

            this.caps.forEach((cap, i) => {
                const left = paddingLeft + ctx.measureText(acc).width;

                if (cap.icon) {
                    overlays.push({i, left, icon: cap.icon});
                }

                acc += (cap.char ?? ' ');
            });

            return overlays;
        },

        syncSelectionFromInput(el) {
            if (!this.caps.length) return;
            const pos = el.selectionStart ?? 0;
            this.selected = Math.min(pos, this.caps.length - 1);
        },
        focusWordInputAt(index) {
            this.$nextTick(() => {
                const el = document.getElementById('bar-builder-word');
                if (!el) return;
                el.focus();
                el.setSelectionRange(index, index);
                this.selected = index;
            });
        },

        get hasSelection() {
            return this.selected >= 0 && this.selected < this.caps.length;
        },
        deselect() {
            this.selected = -1;
        },
        onStageClick(e) {
            const g = e.target.closest('[data-cap]');
            if (!g) return;
            this.press(+g.dataset.cap);
        },
        press(i) {
            this.selected = i;
            this.pressed = i;
            setTimeout(() => {
                this.pressed = null;
            }, 110);
        },
        next() {
            this.selected = this.hasSelection ? (this.selected + 1) % this.caps.length : 0;
        },
        prev() {
            this.selected = this.hasSelection ? (this.selected - 1 + this.caps.length) % this.caps.length : this.caps.length - 1;
        },

        setWord(v) {
            const chars = [...v.toUpperCase()].slice(0, this.MAX);
            while (chars.length < this.MIN) chars.push(' ');
            this.caps = chars.map((ch, i) => {
                const prev = this.caps[i];
                const combo = prev ? {cap: prev.hex, text: prev.textHex} : pick(this.availableCombos);
                const icon = (prev && prev.icon && ch === ' ') ? prev.icon : null;
                return {char: ch, hex: combo.cap, textHex: combo.text, icon};
            });
            this.word = chars.join('').trimEnd();
            this.selected = Math.min(this.selected, this.caps.length - 1);
            this.queueDraftSave();
        },
        setChar(i, ch) {
            this.caps[i].char = ch.toUpperCase();
            this.caps[i].icon = null;
            this.word = this.caps.map(c => c.char).join('').trimEnd();
            this.queueDraftSave();
        },
        setIcon(i, icon) {
            this.caps[i].icon = icon;
            this.caps[i].char = ' ';
            this.word = this.caps.map(c => c.char).join('').trimEnd();
            this.queueDraftSave();
        },
        // Picking an icon while the last cap is selected normally overwrites
        // it and wraps back to cap 1 (via next()). If there's still room,
        // grow the bar with a new icon cap instead so the existing last
        // cap's content is preserved.
        chooseIcon(icon) {
            if (this.selected === this.caps.length - 1 && this.caps.length < this.MAX) {
                this.addCapWithIcon(icon);
                return;
            }
            this.setIcon(this.selected, icon);
            this.next();
            this.focusWordInputAt(this.selected);
        },
        addCapWithIcon(icon) {
            const combo = pick(this.availableCombos);
            this.caps.push({char: ' ', hex: combo.cap, textHex: combo.text, icon});
            this.selected = this.caps.length - 1;
            this.queueDraftSave();
            this.focusWordInputAt(this.selected);
        },
        setCombo(i, combo) {
            this.caps[i].hex = combo.cap;
            this.caps[i].textHex = combo.text;
            this.queueDraftSave();
        },
        setBaseColor(hex) {
            this.baseHex = hex;
            this.queueDraftSave();
        },
        addCap() {
            if (this.caps.length >= this.MAX) return;
            const combo = pick(this.availableCombos);
            this.caps.push({char: ' ', hex: combo.cap, textHex: combo.text, icon: null});
            this.selected = this.caps.length - 1;
            this.queueDraftSave();
        },
        removeCap() {
            if (this.caps.length <= this.MIN) return;
            this.caps.pop();
            this.selected = Math.min(this.selected, this.caps.length - 1);
            this.word = this.caps.map(c => c.char).join('').trimEnd();
            this.queueDraftSave();
        },
        applyToAll() {
            const src = this.caps[this.selected];
            this.caps.forEach(c => {
                c.hex = src.hex;
                c.textHex = src.textHex;
            });
            this.queueDraftSave();
        },

        get currentPrice() {
            return this.CAPS_CATALOG.find(c => c.caps === this.caps.length)?.price ?? '';
        },
        get reference() {
            const w = (this.word.replace(/[^A-Z0-9]/g, '') || 'BAR').slice(0, 6).padEnd(3, 'X');
            return `CB${this.caps.length}-${w}-${this.baseHex.slice(1, 4).toUpperCase()}`;
        },
        get payload() {
            return {
                quantity: 1,
                meta: {
                    text: this.word,
                    base_colour: {name: this.baseColorName(this.baseHex), hex: this.baseHex},
                    caps: this.caps.map((c, i) => ({
                        position: i + 1,
                        character: c.icon ? null : (c.char.trim() || null),
                        icon: c.icon ? {id: c.icon.id, name: c.icon.name} : null,
                        combination: this.comboName(c),
                        colour: {name: this.capColorName(c.hex), hex: c.hex},
                        text_colour: {name: this.capColorName(c.textHex), hex: c.textHex},
                    })),
                    reference: this.reference,
                },
            };
        },

        isAvail(item) {
            return !item || item.available !== false;
        },
        isCombo(cap, combo) {
            return !!cap && cap.hex === combo.cap && cap.textHex === combo.text;
        },
        comboName(cap) {
            if (!cap) return '—';
            return this.CAP_COMBOS.find(c => this.isCombo(cap, c))?.name ?? '{{ __('Custom') }}';
        },
        capColorName(hex) {
            if (!hex) return '—';
            const h = hex.toUpperCase();
            return this.BASE_COLORS.find(c => c.hex.toUpperCase() === h)?.name ?? hex;
        },
        baseColorName(hex) {
            return this.BASE_COLORS.find(c => c.hex === hex)?.name ?? '—';
        },

        async addToCart() {
            await this.$wire.addToCart(this.payload);
        },
        flash(msg) {
            this.toast = msg;
            clearTimeout(this._t);
            this._t = setTimeout(() => this.toast = '', 2600);
        },
    }));

    function hexToRgb(h) {
        const v = h.replace('#', '');
        return [0, 2, 4].map(i => parseInt(v.slice(i, i + 2), 16));
    }

    function shade(hex, amt) {
        const [r, g, b] = hexToRgb(hex);
        const f = v => Math.round(amt < 0 ? v * (1 + amt) : v + (255 - v) * amt);
        return '#' + [f(r), f(g), f(b)].map(v => v.toString(16).padStart(2, '0')).join('');
    }

    function lum(hex) {
        const [r, g, b] = hexToRgb(hex).map(v => {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * r + 0.7152 * g + 0.0722 * b;
    }

    const isDark = hex => lum(hex) < 0.4;
    const pick = arr => arr[Math.floor(Math.random() * arr.length)];
    const esc = s => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

    function rgbToHsl(hex) {
        let [r, g, b] = hexToRgb(hex).map(v => v / 255);
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r:
                    h = (g - b) / d + (g < b ? 6 : 0);
                    break;
                case g:
                    h = (b - r) / d + 2;
                    break;
                default:
                    h = (r - g) / d + 4;
            }
            h /= 6;
        }
        return [h, s, l];
    }

    function hslToHex(h, s, l) {
        let r, g, b;
        if (s === 0) {
            r = g = b = l;
        } else {
            const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            const p = 2 * l - q;
            const f = (p, q, t) => {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1 / 6) return p + (q - p) * 6 * t;
                if (t < 1 / 2) return q;
                if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                return p;
            };
            r = f(p, q, h + 1 / 3);
            g = f(p, q, h);
            b = f(p, q, h - 1 / 3);
        }
        const to = v => Math.round(v * 255).toString(16).padStart(2, '0');
        return '#' + to(r) + to(g) + to(b);
    }

    function contrastRatio(a, b) {
        const la = lum(a), lb = lum(b), hi = Math.max(la, lb), lo = Math.min(la, lb);
        return (hi + 0.05) / (lo + 0.05);
    }

    function ringFor(baseHex) {
        const [h, s] = rgbToHsl(baseHex);
        const light = hslToHex(h, Math.min(0.90, s + 0.06), 0.78);
        const dark = hslToHex(h, Math.min(0.95, s + 0.10), 0.30);
        return contrastRatio(baseHex, dark) >= contrastRatio(baseHex, light) ? dark : light;
    }
</script>
@endscript
