import RenturoTextLogoBlue from '@/assets/logo/RenturoLogoBlue.png';

function Header() {
    return (
        <header>
            <div className='3xl:max-w-screen-2xl mx-auto flex max-w-screen-lg items-center justify-between p-4 2xl:max-w-screen-xl'>
                <div>
                    <img
                        src={RenturoTextLogoBlue}
                        alt='app logo'
                        className='h-[74px]'
                    />
                </div>
                <ul className='flex cursor-pointer gap-8 text-[25px] font-medium text-black/70'>
                    <li className='text-black transition hover:font-bold'>
                        Home
                    </li>
                    <li className='text-black transition hover:font-bold'>
                        About Us
                    </li>
                    <li className='text-black transition hover:font-bold'>
                        Download
                    </li>
                    <li className='text-black transition hover:font-bold'>
                        Contact Us
                    </li>
                </ul>
                <button className='h-[60px] w-[228px] rounded-lg bg-metalic-blue text-[25px] font-bold text-white'>
                    Get Started
                </button>
            </div>
        </header>
    );
}

export default Header;
