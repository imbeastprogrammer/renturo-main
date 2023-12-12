import { PropsWithChildren } from 'react';
import Navbar from './Navbar';

function AdsPartnerLayout(props: PropsWithChildren) {
    return (
        <div className='grid h-screen grid-rows-[auto_1fr] overflow-auto font-outfit'>
            <Navbar />
            <main className='bg-[#F9F9F9] p-4'>{props.children}</main>
        </div>
    );
}

export default AdsPartnerLayout;
