import Breadcrumb from './Breadcrumb';
import UserButton from '@/components/tenant/UserButton';

import { LabelMap } from '.';

export const replaceNumberWithId = (route: string) =>
    route.replace(/\/(\d+)(\/|$)/g, '/{id}$2');

function AdminLayoutHeader() {
    const { pathname } = window.location;

    return (
        <header>
            <div className='flex items-center justify-between gap-4 pb-4'>
                <div>
                    <h1 className='text-[30px] font-semibold'>
                        {LabelMap[replaceNumberWithId(pathname)]}
                    </h1>
                    <Breadcrumb />
                </div>
                <UserButton />
            </div>
        </header>
    );
}

export default AdminLayoutHeader;
