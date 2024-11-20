<x-layouts.admin.profile-management-layout>
    <x-section width="w-full" class="!p-2">
        @if ($errors->any())
            @dump($errors)
        @endif
        <x-table>
            <x-slot name="header">
                <tr class="bg-gray-700 text-white h-12 text-left">
                    <th class="pl-5">Name</th>
                    <th>E-mail</th>
                    <th class="pr-5 text-end">Actions</th>
                </tr>
            </x-slot>

            <x-slot name="content">
                @foreach($users as $user)
                    <tr class="outline-none h-16 border border-gray-100 rounded even:bg-gray-50">
                        <td class="">
                            <div class="flex flex-row align-center align-items-start pl-5">
                                <div>
                                @if ($user->disabled_at)
                                    <div class="inline pr-1" x-data="{ tooltip: false }" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">
                                        <i class="fa fa-ban text-red-600"></i>
                                        <div x-show="tooltip" class="text-sm text-wrap text-white absolute bg-red-600 rounded-lg max-w-xl p-2 transform -translate-y-1 -translate-x-5">
                                            @if($user->disabled_reason)<span><i class="fa fa-ban"></i>  {{ $user->disabled_reason }}</span>@endif
                                            <span class="pt-1 block text-xs font-semibold">Disabled at: {{ $user->disabled_at->format('d-m-Y @ H:m') }}</span>
                                        </div>
                                    </div>
                                @endif

                                <p class="inline text-base font-semibold leading-none text-gray-700 mr-2">
                                    {{ $user->name }}
                                </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-base text-gray-700 leading-5">
                                {{ $user->email }}
                            </p>
                        </td>
                        <td class="flex items-center justify-end h-16 pr-6">
                            <div class="flex border rounded overflow-hidden">
                                <button
                                    x-data="" x-on:click.prevent="$dispatch('open-modal', 'user-information-{{ $user->id }}')"
                                    class="bg-white hover:bg-gray-200 cursor-pointer border px-2 py-1 disabled:cursor-not-allowed disabled:bg-gray-200" disabled>
                                    <i class="fa fa-circle-info"></i>
                                </button>

                                <x-modal name="user-information-{{ $user->id }}" focusable>
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('User information') }}
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600 text-wrap">
                                            {{ __('No information to show... yet!') }}
                                        </p>
                                    </div>
                                </x-modal>

                                @if (!$user->isAdmin())
                                    @if (!$user->disabled_at)
                                        <button
                                            x-data="" x-on:click.prevent="$dispatch('open-modal', 'user-disable-{{ $user->id }}')"
                                            class="bg-white hover:bg-gray-200 cursor-pointer border px-2 py-1">
                                            <i class="fa-solid fa-lock"></i>
                                        </button>

                                        <x-modal name="user-disable-{{ $user->id }}" focusable>
                                            <form method="post" action="{{ route('admin.management.profile.disable', $user) }}" class="p-6">
                                                @csrf
                                                @method('post')

                                                <h2 class="text-lg font-medium text-gray-900">
                                                    {{ __('Are you sure you want to disable the account from') }} <span class="font-semibold">{{ $user->name }}</span>?
                                                </h2>

                                                <p class="mt-1 text-sm text-gray-600 text-wrap">
                                                    {{ __('Once the user is disabled, all of its resources and data will be retained but the user will not be able to login & see their data. You can still make the decision to unlock the account or fully delete afterwards.') }}
                                                </p>

                                                <p class="mt-1 text-sm text-gray-600 text-wrap">
                                                    {{ __('Please write an explanation why the account will be disabled.') }}
                                                </p>

                                                <div class="mt-6">
                                                    <x-form.field.partials.label for="reason" value="{{ __('reason') }}" class="sr-only" />

                                                    <x-form.field.partials.text
                                                        id="reason"
                                                        name="reason"
                                                        type="text"
                                                        class="mt-1 block w-3/4"
                                                        placeholder="{{ __('Explanation') }}"
                                                    />

                                                    <x-form.field.partials.error :messages="$errors->userDisabled->get('reason')" class="mt-2" />
                                                </div>

                                                <div class="mt-6 flex justify-end">
                                                    <x-secondary-button x-on:click="$dispatch('close')">
                                                        {{ __('Cancel') }}
                                                    </x-secondary-button>

                                                    <x-danger-button class="ml-3" >
                                                        {{ __('Disable Account') }}
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    @else
                                        <a x-data="" x-on:click.prevent="$dispatch('open-modal', 'user-enable-{{ $user->id }}')"
                                            class="bg-white hover:bg-gray-200 cursor-pointer border px-2 py-1">
                                            <i class="fa fa-unlock"></i>
                                        </a>

                                        <x-modal name="user-enable-{{ $user->id }}" focusable>
                                            <form method="post" action="{{ route('admin.management.profile.enable', $user) }}" class="p-6">
                                                @csrf
                                                @method('patch')

                                                <h2 class="text-lg font-medium text-gray-900">
                                                    {{ __('Are you sure you want to enable the account from') }} <span class="font-semibold">{{ $user->name }}</span>?
                                                </h2>

                                                <p class="mt-1 text-sm text-gray-600 text-wrap">
                                                    {{ __('Once the user is enable, all of its resources and data will be available for the user & login again.') }}
                                                </p>

                                                @if($user->disabled_reason)
                                                    <p class="mt-1 text-sm text-gray-600 text-wrap">
                                                        {{ __('Reason:') }} <span class="text-red-600 font-semibold">{{ $user->disabled_reason }}</span>
                                                    </p>
                                                @endif

                                                <div class="mt-6 flex justify-end">
                                                    <x-secondary-button x-on:click="$dispatch('close')">
                                                        {{ __('Cancel') }}
                                                    </x-secondary-button>

                                                    <x-success-button class="ml-3" >
                                                        {{ __('Enable Account') }}
                                                    </x-success-button>
                                                </div>
                                            </form>
                                        </x-modal>

                                        <a x-data="" x-on:click.prevent="$dispatch('open-modal', 'user-deletion-{{ $user->id }}')"
                                           class="bg-white hover:bg-gray-200 cursor-pointer border px-2 py-1">
                                            <i class="fa fa-trash"></i>
                                        </a>

                                        <x-modal name="user-deletion-{{ $user->id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
                                            <form method="post" action="{{ route('admin.management.profile.delete', $user) }}" class="p-6">
                                                @csrf
                                                @method('delete')

                                                <h2 class="text-lg font-medium text-gray-900">
                                                    {{ __('Are you sure you want to delete the account from') }} <span class="font-semibold">{{ $user->name }}</span>?
                                                </h2>

                                                <p class="mt-1 text-sm text-gray-600">
                                                    {{ __('Once this account is deleted, all of its resources and data will be permanently deleted.') }}
                                                </p>

                                                <div class="mt-6 flex justify-end">
                                                    <x-secondary-button x-on:click="$dispatch('close')">
                                                        {{ __('Cancel') }}
                                                    </x-secondary-button>

                                                    <x-danger-button class="ml-3" >
                                                        {{ __('Delete Account') }}
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot>
        </x-table>
    </x-section>
</x-layouts.admin.profile-management-layout>
