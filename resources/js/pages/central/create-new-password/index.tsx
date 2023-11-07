import CreateNewPasswordForm from './components/CreateNewPasswordForm';
import CreateNewPasswordHero from './components/CreateNewPasswordHero';

function CreateNewPassword() {
    return (
        <div className='font-outfit grid h-screen place-items-center p-4'>
            <div className='grid h-full max-h-[700px] w-full max-w-6xl grid-cols-[400px_1fr] rounded-3xl border p-4'>
                <CreateNewPasswordHero />
                <CreateNewPasswordForm />
            </div>
        </div>
    );
}

export default CreateNewPassword;
