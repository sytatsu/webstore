<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\ProfileManagementCreateRequest;
use App\Http\Requests\Management\ProfileManagementDeleteRequest;
use App\Http\Requests\Management\ProfileManagementDisableRequest;
use App\Models\User;
use App\Services\ProfileService;
use App\Services\ToastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileManagementController extends Controller
{
    public function __construct(
        public ProfileService $profileService,
        public ToastService $toastService,
    ) {
        $this->middleware(['auth', 'is.admin']);
    }

    public function index(): View
    {
        return view('admin.profile-management-list', [
            'users' => User::all(),
        ]);
    }

    public function create(): View
    {
        return view('admin.profile-management-create');
    }

    public function store(ProfileManagementCreateRequest $request): RedirectResponse
    {
        $this->profileService->create(attributes: $request->validated());

        $this->toastService->create('success', 'Successfully created account');

        Return Redirect::route('admin.management.profile.index')->with('status', 'createdUser');
    }

    public function disable(ProfileManagementDisableRequest $request, User $user): RedirectResponse
    {
        $this->profileService->disable(
            user: $user,
            reason: $request->get('reason'),
        );

        $this->toastService->create('success', 'Successfully disabled account');

        return Redirect::route('admin.management.profile.index');
    }

    public function enable(User $user): RedirectResponse
    {
        $this->profileService->enable(user: $user);

        $this->toastService->create('success', 'Successfully enabled account');

        return Redirect::route('admin.management.profile.index')->with('status', 'enabledUser');
    }

    public function delete(ProfileManagementDeleteRequest $request, User $user): RedirectResponse
    {
        $this->profileService->destroy(
            user: $user,
            session: $request->session(),
        );

        $this->toastService->create('success', 'Successfully deleted account');

        return Redirect::route('admin.management.profile.index')->with('status', 'deletedUser');
    }
}
