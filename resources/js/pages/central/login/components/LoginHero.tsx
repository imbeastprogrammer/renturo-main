import { LoginHeroLogo } from '@/assets/central/auth';
import KabootekTextLogoWhite from '@/assets/central/auth/kabootek-text-logo-white.png';

function LoginHero() {
    return (
        <div className='bg-yinmn-blue relative grid place-items-center rounded-lg p-8'>
            <img
                className='absolute right-6 top-6 h-[36px]'
                src={KabootekTextLogoWhite}
                alt='logo'
            />
            <LoginHeroLogo />
        </div>
    );
}

export default LoginHero;
