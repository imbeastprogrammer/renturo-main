import { ReactNode } from 'react';
import OwnerLayout from '@/layouts/OwnerLayout';

function PostManagement() {
    return <div>PostManagement</div>;
}

PostManagement.layout = (page: ReactNode) => <OwnerLayout>{page}</OwnerLayout>;

export default PostManagement;
