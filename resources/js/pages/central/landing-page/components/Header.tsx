import RenturoTextLogoBlue from '@/assets/logo/RenturoLogoBlue.png';

function Header() {
    return (
        <header>
            <div className='mx-auto flex max-w-[1556px] items-center justify-between p-4'>
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
