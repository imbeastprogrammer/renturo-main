import { PropsWithChildren } from 'react';
import OwnerSidebar from './OwnerSidebar';

function OwnerLayout(props: PropsWithChildren) {
    return (
        <div className='grid h-screen grid-cols-[auto_1fr]'>
            <OwnerSidebar />
            <main>{props.children}</main>
        </div>
    );
}

export default OwnerLayout;
