import { ArrowRightIcon } from 'lucide-react';
import { HomeHero, HomeHero2, HomeHero3 } from '@/assets/central/landing-page';
import { Button } from '@/components/ui/button';

function Home() {
    return (
        <div className='mx-auto my-10 w-full max-w-[1556px] space-y-10'>
            <section className='grid grid-rows-[1fr_1fr] gap-8 p-4 xl:grid-cols-2 xl:grid-rows-1'>
                <div className='space-y-8'>
                    <h1 className='text-[32px] font-semibold leading-none xl:text-[94px]'>
                        Welcome to your{' '}
                        <span className='text-arylide-yellow'>
                            one-stop shop
                        </span>{' '}
                        for all your needs.
                    </h1>
                    <p className='text-[15px] xl:text-[34px]'>
                        With a vast selection and a{' '}
                        <span className='font-semibold text-metalic-blue'>
                            hassle-free experience with Renturo
                        </span>
                        , we're here to make your life easier and more
                        enjoyable.
                    </p>
                    <Button className='gap-4 bg-metalic-blue text-sm font-semibold hover:bg-metalic-blue/90'>
                        Sign Up
                        <ArrowRightIcon />
                    </Button>
                </div>
                <div>
                    <img
                        src={HomeHero}
                        className='w-full object-contain'
                        alt='hero img'
                    />
                </div>
            </section>
            <section className='grid grid-rows-1 gap-x-8 p-4 xl:grid-cols-2'>
                <div className='hidden'>
                    <img src={HomeHero2} alt='hero img' />
                </div>
                <article className='space-y-8'>
                    <h2 className='text-[32px] font-bold xl:text-[64px]'>
                        Lorem <span className='text-metalic-blue'>Ipsum</span>
                    </h2>
                    <p className='text-[15px] xl:text-[32px]'>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor
                    </p>
                    <div className='grid w-max gap-4 gap-y-8 xl:grid-cols-2'>
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
                    <button className='flex h-[43px] w-full items-center justify-center gap-2 rounded-lg border border-metalic-blue text-[26px] text-lg font-medium leading-none text-metalic-blue xl:h-[81px] xl:w-[274px]'>
                        More About Us
                        <ArrowRightIcon />
                    </button>
                </article>
            </section>
            <section className='grid grid-rows-[auto_auto] gap-10 p-4 xl:grid-cols-[2fr_1fr] xl:grid-rows-1'>
                <div className='flex items-start rounded-xl bg-arylide-yellow/10 p-4'>
                    <h2 className='text-[36px] font-bold leading-none xl:text-[64px]'>
                        Start your business account today!
                    </h2>
                    <img
                        src={HomeHero3}
                        alt='hero img'
                        className='w-full max-w-[180px] translate-y-[100px] self-end object-cover'
                    />
                </div>
                <div className='flex flex-col justify-center gap-y-4 rounded-xl bg-jasper-orange/10 p-8'>
                    <p className='max-w-[18ch] text-xl xl:text-[25px]'>
                        Don't miss out on the opportunity to connect with
                        potential customers.
                    </p>
                    <button className='flex items-center gap-2 text-lg font-semibold xl:text-[25px]'>
                        Join now{' '}
                        <ArrowRightIcon className='xl:h-[25px] xl:w-[25px]' />
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
            <h1 className='text-[25px] font-bold leading-none text-metalic-blue xl:text-[50px]'>
                {title}
            </h1>
            <p className='text-[15px] text-black/90 xl:text-[24px]'>
                {description}
            </p>
        </div>
    );
}

export default Home;
