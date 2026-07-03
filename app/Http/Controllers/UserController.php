<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Models\Organisation;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return view('pages.users.index');
    }

    public function create()
    {
        $organisations = [];
        if (Auth::user()->role === 'super_admin') {
            $organisations = Organisation::all();
        }

        $html = view('pages.users.create', compact('organisations'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Create User'
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:super_admin,org_admin,employee',
        ];

        if (Auth::user()->role === 'super_admin') {
            $rules['organisation_id'] = 'required|exists:organisations,id';
        }

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $validated = $validator->validated();
        $validated['password'] = bcrypt($validated['password']);

        $this->userRepository->create($validated);

        return $this->ajaxResponse('success', 'User created successfully!', [
        ]);
    }
}
