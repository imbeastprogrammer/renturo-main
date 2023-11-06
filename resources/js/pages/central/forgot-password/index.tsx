import ForgotPasswordForm from './components/ForgotPasswordForm';
import ForgotPasswordHero from './components/ForgotPasswordHero';

function ForgotPassword() {
    return (
        <div className='grid h-screen place-items-center p-4'>
            <div className='grid h-full max-h-[700px] w-full max-w-5xl grid-cols-[400px_1fr] rounded-3xl border p-4 shadow-lg'>
                <ForgotPasswordHero />
                <ForgotPasswordForm />
            </div>
        </div>
    );
}

export default ForgotPassword;
