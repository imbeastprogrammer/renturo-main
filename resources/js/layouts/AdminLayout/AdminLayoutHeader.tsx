import UserButton from '@/components/tenant/UserButton';
import Breadcrumb from './Breadcrumb';
import { LabelMap } from '.';

function AdminLayoutHeader() {
    const { pathname } = window.location;

    return (
        <header>
            <div className='flex items-center justify-between gap-4 pb-4'>
                <div>
                    <h1 className='text-[30px] font-semibold'>
                        {LabelMap[pathname]}
                    </h1>
                    <Breadcrumb />
                </div>
                <UserButton />
            </div>
        </header>
    );
}

export default AdminLayoutHeader;
