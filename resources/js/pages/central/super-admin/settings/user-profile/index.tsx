import SuperAdminLayout from '@/layouts/SuperAdminLayout';

function UpdateProfile() {
    return <div>User Profile</div>;
}

UpdateProfile.layout = (page: any) => <SuperAdminLayout>{page}</SuperAdminLayout>;

export default UpdateProfile;
