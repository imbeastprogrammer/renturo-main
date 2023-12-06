import { PropsWithChildren } from 'react';
import { FiMenu } from 'react-icons/fi';
import RenturoTextLogoBlue from '@/assets/logo/RenturoLogoBlue.png';

function Header() {
    return (
        <header className='sticky top-0 z-[1000] bg-white'>
            <div className='mx-auto flex max-w-screen-lg items-center justify-between p-4 2xl:max-w-screen-xl 3xl:max-w-screen-2xl'>
                <div>
                    <img
                        src={RenturoTextLogoBlue}
                        alt='app logo'
                        className='h-[36px] 2xl:h-[74px]'
                    />
                </div>
                <ul className='hidden cursor-pointer gap-8 font-medium text-black/70 md:text-xl xl:flex 2xl:text-[25px]'>
                    <NavLink>Home</NavLink>
                    <NavLink>About Us</NavLink>
                    <NavLink>Download</NavLink>
                    <NavLink>Contact Us</NavLink>
                </ul>
                <button className='hidden h-[40px] w-[170px] rounded-lg bg-metalic-blue  text-xl font-bold text-white lg:block 2xl:h-[60px] 2xl:w-[228px] 2xl:text-[25px]'>
                    Get Started
                </button>
                <button className='md:hidden'>
                    <FiMenu className='h-[30px] w-[30px] text-metalic-blue' />
                </button>
            </div>
        </header>
    );
}

type NavLinkProps = PropsWithChildren;
function NavLink({ children }: NavLinkProps) {
    return <li className='font-medium text-black/70 transition'>{children}</li>;
}

export default Header;
