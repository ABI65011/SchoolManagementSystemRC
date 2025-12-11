<?php
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User validation mwahahahaha.. 
            "name" => "required",
            "email" => "required|unique:users,email",
            "role" => "required|in:" . implode(",", array_column(UserRoles::cases(), "value")),

            // Guardian validation inevitable.... 
            "title" => "required|in:" . implode(',', array_column(Title::cases(), 'value')),
            "relationship" => "required|in:" . implode(',', array_column(Relationship::cases(), 'value')),
            "identification_image" => "required",
            "gender" => "required|in:" . implode(',', array_column(Gender::cases(), 'value')),
            "dob" => "required",
            "marital_status" => "required|in:" . implode(',', array_column(Maritalstatus::cases(), 'value')),

            // Contact inform...
            "primary" => "required",
            "secondary" => "required",
            // "email" => "nullable",

            //Emergency contact...
            "full_name" => "required",
            "rlsh_to_student" => "required|in:" . implode(",", array_column(Relationship::cases(), "value")),
            "primary_phone" => "required",
            "secondary_phone" => "nullable",

            //Address
            "country" => "required|in:" . implode(",", array_column(Country::cases(), "value")),
            "district" => "required",
            "sub_county" => "nullable",
            "village" => "nullable",

            // Emplotment info...
            "profession" => "required",
            "phone" => "nullable",
            "address" => "nullable",
        ]);

        if ($validator->fails()) {
            return back()
                ->with('validation', 'Check the fields')
                ->withErrors($validator->errors())
                ->withInput();
        }

        $validated = $validator->validated();
        $validated['password'] = Str::uuid();

        // dd($validated);

        try {
            DB::beginTransaction();

            $user = User::create($validated);
            $validated['user_id'] = $user->id;

            $validated['identification_image'] = "tmp-file.png";
            $guardian = Guardian::create($validated);

            Contact::create($validated);
            EmergencyContact::create($validated);
            Address::create($validated);
            Employment::create($validated);

            DB::commit();

            $user->syncRoles($validated['role']);

            $token = Password::createToken($user);
            $user->sendPasswordResetNotification($token);

            $validated['identification_image'] = $request->file('identification_image')->store('guardian', 'public');

            $guardian->update($validated);
            return back()->with('success', 'Guardian data saved successfully! Password resent has been sent!');
        } catch (\Throwable $th) {
            DB::rollBack();
            Storage::delete($validated['identification_image']);
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))->withInput();
        }
    }

