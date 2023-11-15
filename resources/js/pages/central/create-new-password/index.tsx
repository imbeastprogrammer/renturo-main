import CreateNewPasswordForm from './components/CreateNewPasswordForm';
import CreateNewPasswordHero from './components/CreateNewPasswordHero';

function CreateNewPassword() {
    return (
        <div className='grid h-screen w-full grid-cols-[610px_1fr] gap-8 font-outfit'>
            <CreateNewPasswordHero />
            <CreateNewPasswordForm />
        </div>
    );
}

export default CreateNewPassword;
