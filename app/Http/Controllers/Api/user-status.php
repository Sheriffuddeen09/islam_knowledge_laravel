Route::middleware('auth:sanctum')->get('/user-status', function(Request $request) {
    $user = $request->user();
    if ($user) {
        return response()->json([
            'status' => 'logged_in',
            'user' => $user
        ]);
    }

    // If user has started registration but not logged in
    // (example: email exists in DB but no login)
    return response()->json(['status' => 'registered']);
});
