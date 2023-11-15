import LoginForm from './components/LoginForm';
import LoginHero from './components/LoginHero';

function Login() {
    return (
        <div className='grid h-screen w-full grid-cols-[610px_1fr] gap-8 font-outfit'>
            <LoginHero />
            <LoginForm />
        </div>
    );
}

export default Login;
