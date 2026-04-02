import './bootstrap';
import 'preline';

document.addEventListener('livewire:navigated', () => {
    window.HSStaticMethods.autoInit();
});

document.addEventListener('livewire:initialized', () => {
    Livewire.hook('request', ({ detail, succeed }) => {
        succeed(() => {
            queueMicrotask(() => {
                window.HSStaticMethods.autoInit();
            });
        });
    });
});

import.meta.glob([
    '/resources/images/**',
    '/resources/site.manifest'
]);
