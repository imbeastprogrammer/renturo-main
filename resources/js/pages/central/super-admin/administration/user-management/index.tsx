import SuperAdminLayout from '@/layouts/SuperAdminLayout';

function UserManagement() {
    return <div>User management</div>;
}

UserManagement.layout = (page: any) => (
    <SuperAdminLayout>{page}</SuperAdminLayout>
);

export default UserManagement;
