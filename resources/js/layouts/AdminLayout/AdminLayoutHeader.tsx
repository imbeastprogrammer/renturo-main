import Breadcrumb from './Breadcrumb';
import UserButton from '@/components/tenant/UserButton';

import { LabelMap } from '.';

const replaceStringWithId = (route: string) => {
    const keywords = ['update/', 'edit/', 'delete/', 'view/'];

    keywords.forEach((keyword) => {
        const regexKeyword = new RegExp(`(${keyword}[^/]+)`, 'g');
        route = route.replace(regexKeyword, `${keyword}{id}`);
    });

    route = route.replace(/\/(\d+)(\/|$)/g, '/{id}$2');

    return route;
};
function AdminLayoutHeader() {
    const { pathname } = window.location;

    return (
        <header>
            <div className='flex items-center justify-between gap-4 pb-4'>
                <div>
                    <h1 className='text-[30px] font-semibold'>
                        {LabelMap[replaceStringWithId(pathname)]}
                    </h1>
                    <Breadcrumb />
                </div>
                <UserButton />
            </div>
        </header>
    );
}

export default AdminLayoutHeader;
