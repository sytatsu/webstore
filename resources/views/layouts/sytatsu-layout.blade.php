<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <livewire:sytatsu.components.partials.head :title="$title ?? null" appName="Sytatsu"/>

    @if (isset($center) && $center === true)
        <body class="flex flex-col bg-gradient-to-br min-h-screen from-[#FFF1EA] from-10% to-[#FFFBF4] to-90% justify-center content-center">
            {{ $slot }}
        </body>
    @else
        <body class="bg-gradient-to-br h-dvh from-[#FFF1EA] from-10% to-[#FFFBF4] to-90% bg-no-repeat bg-fixed">
            {{ $slot }}
        </body>
    @endif
</html>
