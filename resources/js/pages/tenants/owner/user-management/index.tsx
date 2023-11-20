import { ReactNode } from 'react';
import OwnerLayout from '@/layouts/OwnerLayout';

function UserManagement() {
    return <div>UserManagement</div>;
}

UserManagement.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default UserManagement;
