import { ArrowRightIcon } from 'lucide-react';
import { HomeHero, HomeHero2, HomeHero3 } from '@/assets/central/landing-page';

function Home() {
    return (
        <div className='mx-auto my-40 max-w-[1556px] space-y-20'>
            <section className='grid grid-cols-2 gap-x-8 p-4'>
                <div className='space-y-8'>
                    <h1 className='text-[94px] font-semibold leading-none'>
                        Welcome to your{' '}
                        <span className='text-arylide-yellow'>
                            one-stop shop
                        </span>{' '}
                        for all your needs.
                    </h1>
                    <p className='text-[34px]'>
                        With a vast selection and a{' '}
                        <span className='font-semibold text-metalic-blue'>
                            hassle-free experience with Renturo
                        </span>
                        , we're here to make your life easier and more
                        enjoyable.
                    </p>
                    <p className='flex items-center gap-2 text-[34px] font-semibold  text-metalic-blue hover:underline'>
                        Sign Up for a Hassle-Free Experience
                        <ArrowRightIcon className='h-[34px] w-[34px] text-metalic-blue' />
                    </p>
                </div>
                <div>
                    <img src={HomeHero} alt='hero img' />
                </div>
            </section>
            <section className='grid grid-cols-2 gap-x-8 p-4'>
                <div>
                    <img src={HomeHero2} alt='hero img' />
                </div>
                <article className='space-y-8'>
                    <h2 className='text-[64px] font-bold'>
                        Lorem <span className='text-metalic-blue'>Ipsum</span>
                    </h2>
                    <p className='text-[32px]'>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor
                    </p>
                    <div className='grid w-max grid-cols-2 gap-4 gap-y-8'>
                        <HomeStat title='100k+' description='Total Customers' />
                        <HomeStat
                            title='100k+'
                            description='Total Business Owners'
                        />
                        <HomeStat
                            title='100k%'
                            description='Customer Satisfaction'
                        />
                    </div>
                    <button className='flex h-[81px] w-[274px] items-center justify-center gap-2 rounded-lg border border-metalic-blue text-[26px] font-medium leading-none text-metalic-blue'>
                        More About Us
                        <ArrowRightIcon className='h-[26px] w-[26px]' />
                    </button>
                </article>
            </section>
            <section className='grid grid-cols-[3fr_1fr] gap-x-8 p-4'>
                <div className='flex h-[324px] items-center rounded-xl bg-arylide-yellow/10 p-8'>
                    <h2 className='text-[64px] font-bold leading-none'>
                        Start your business account today!
                    </h2>
                    <img
                        src={HomeHero3}
                        alt='hero img'
                        className='self-start'
                    />
                </div>
                <div className='flex flex-col justify-center gap-y-4 rounded-xl bg-jasper-orange/10 p-8'>
                    <p className='text-[25px]'>
                        Don't miss out on the opportunity to connect with
                        potential customers.
                    </p>
                    <button className='flex items-center gap-2 text-[25px] font-semibold'>
                        Join now{' '}
                        <ArrowRightIcon className='h-[25px] w-[25px]' />
                    </button>
                </div>
            </section>
        </div>
    );
}

type HomeStatProps = {
    title: string;
    description: string;
};
function HomeStat({ title, description }: HomeStatProps) {
    return (
        <div>
            <h1 className='text-[50px] font-bold leading-none text-metalic-blue'>
                {title}
            </h1>
            <p className='text-[24px] text-black/90'>{description}</p>
        </div>
    );
}

export default Home;
