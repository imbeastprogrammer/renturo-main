import LoginForm from './components/LoginForm';
import LoginHero from './components/LoginHero';

function Login() {
    return (
        <div className='grid h-screen place-items-center p-4'>
            <div className='grid h-full max-h-[700px] w-full max-w-5xl grid-cols-[1fr_400px] gap-8 rounded-3xl border p-4 shadow-lg'>
                <LoginForm />
                <LoginHero />
            </div>
        </div>
    );
}

export default Login;
