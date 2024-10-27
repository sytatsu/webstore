class welcome {
    constructor() {
        /**
         * Target Reader variables
         */
        this.reader = {
            'selector' : '[data-function=target-reader]',
        };

        this.eventListeners();
    }

    eventListeners()
    {
        document.querySelectorAll(this.reader.selector).forEach((element) => {
            element.addEventListener('click', (event) => {
                this.targetReader(event);
            });
        });
    }

    targetReader(event)
    {
        event.preventDefault();

        const readerTarget = event.target.getAttribute('data-target');

        return readerTarget.startsWith('http')
            ? window.open(readerTarget, '_blank')
            : window.open(readerTarget);
    }
}

new welcome();
