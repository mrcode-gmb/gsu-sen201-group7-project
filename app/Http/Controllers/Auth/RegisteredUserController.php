<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Traits\BusinessScoped;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    use BusinessScoped;
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    public function createUser()
    {
        return view("users.create");
    }
    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', 'in:admin,salesperson'],
        ]);

        // Auto-assign business_id from the logged-in admin
        $data = $this->addBusinessId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make("1234"),
            'role' => $request->role,
        ]);

        $user = User::create($data);

        event(new Registered($user));

        return redirect()->route('admin.myStaff')->with('success', 'Staff created successfully! Default password is: 1234');
    }

    public function showUser(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function editUser(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'role' => ['required', 'string', 'in:admin,salesperson'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.myStaff')->with('success', 'Staff updated successfully!');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Staff activated successfully!' : 'Staff deactivated successfully!';
        return redirect()->route('admin.myStaff')->with('success', $message);
    }
}
