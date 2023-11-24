import ForgotPasswordOtpForm from './components/ForgotPasswordOtpForm';
import ForgotPasswordHero from './components/ForgotPasswordHero';

function ForgotPasswordOtp() {
    return (
        <div className='grid h-screen w-full grid-cols-[610px_1fr] gap-8 font-outfit'>
            <ForgotPasswordHero />
            <ForgotPasswordOtpForm />
        </div>
    );
}

export default ForgotPasswordOtp;
