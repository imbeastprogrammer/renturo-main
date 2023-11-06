import { LoginHeroLogo } from '@/assets/central/login';
import renturoLogoWhite from '@/assets/logo/RenturoLogoWhite.png';

function LoginHero() {
    return (
        <div className='relative grid place-items-center rounded-lg bg-gradient-to-br from-[#185ADC] to-[#1B49A5] p-8'>
            <img
                className='absolute right-8 top-8 h-[36px]'
                src={renturoLogoWhite}
                alt='logo'
            />
            <LoginHeroLogo />
        </div>
    );
}

export default LoginHero;
