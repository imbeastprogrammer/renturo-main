function SettingsNavigation() {
    return (
        <div className='grid'>
            <h1 className='mb-2 font-semibold uppercase text-gray-400'>
                Settings
            </h1>
            <ul className='space-y-2'>
                <li className='cursor-pointer font-semibold hover:underline'>
                    Reset Password
                </li>
                <li className='cursor-pointer font-semibold hover:underline'>
                    Log Activities
                </li>
                <li className='cursor-pointer font-semibold hover:underline'>
                    Roles
                </li>
                <li className='cursor-pointer font-semibold text-red-500 hover:underline'>
                    Delete User
                </li>
            </ul>
        </div>
    );
}

export default SettingsNavigation;
