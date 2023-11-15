import ForgotPasswordForm from './components/ForgotPasswordForm';
import ForgotPasswordHero from './components/ForgotPasswordHero';

function ForgotPassword() {
    return (
        <div className='grid h-screen w-full grid-cols-[610px_1fr] gap-8 font-outfit'>
            <ForgotPasswordHero />
            <ForgotPasswordForm />
        </div>
    );
}

export default ForgotPassword;
