import { ArrowRightIcon } from 'lucide-react';
import { HomeHero, HomeHero2, HomeHero3 } from '@/assets/central/landing-page';
import { Button } from '@/components/ui/button';

function Home() {
    return (
        <div className='mx-auto my-10 w-full max-w-screen-lg space-y-10 xl:space-y-20 2xl:max-w-screen-xl 3xl:max-w-screen-2xl'>
            <section className='grid grid-rows-[1fr_1fr] items-center gap-4 p-4 md:grid-cols-2 md:grid-rows-1 xl:grid-rows-1 2xl:gap-20'>
                <div className='space-y-8'>
                    <h1 className='text-[32px] font-semibold leading-none md:text-[40px] 2xl:text-[65px]'>
                        Welcome to your{' '}
                        <span className='text-arylide-yellow'>
                            one-stop shop
                        </span>{' '}
                        for all your needs.
                    </h1>
                    <p className='text-[15px] md:text-[24px] 2xl:text-[30px]'>
                        With a vast selection and a{' '}
                        <span className='font-semibold text-metalic-blue'>
                            hassle-free experience with Renturo
                        </span>
                        , we're here to make your life easier and more
                        enjoyable.
                    </p>
                    <Button className='gap-4 bg-metalic-blue text-sm font-semibold hover:bg-metalic-blue/90 md:text-[24px] 2xl:hidden'>
                        Sign Up
                        <ArrowRightIcon />
                    </Button>
                    <Button className='hidden h-[68px] w-max items-center gap-4 rounded-xl bg-metalic-blue px-8 text-[30px] font-medium hover:bg-metalic-blue/90 2xl:flex'>
                        Sign Up for a Hassle-Free Experience
                        <ArrowRightIcon className='h-[28px] w-[28px]' />
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
            <section className='grid grid-rows-1 items-center gap-x-8 p-4 md:grid-cols-2'>
                <div className='hidden md:block'>
                    <img
                        src={HomeHero2}
                        alt='hero img'
                        className='h-full w-full object-contain md:max-w-[350px] 2xl:max-h-[549px] 2xl:max-w-[508px]'
                    />
                </div>
                <article className='space-y-4 xl:space-y-8'>
                    <h2 className='text-[32px] font-bold md:text-[40px] 2xl:text-[64px]'>
                        Lorem <span className='text-metalic-blue'>Ipsum</span>
                    </h2>
                    <p className='text-[15px] md:text-[24px] 2xl:text-[30px]'>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor
                    </p>
                    <div className='grid gap-4 md:grid-cols-2 2xl:gap-y-8'>
                        <HomeStat title='100k+' description='Total Customers' />
                        <HomeStat
                            title='100k+'
                            description='Total Business Owners'
                        />
                        <HomeStat
                            title='100k+'
                            description='Customer Satisfaction'
                        />
                    </div>
                    <Button className='h-[43px] w-full cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-metalic-blue bg-transparent text-lg font-medium leading-none text-metalic-blue transition hover:bg-metalic-blue hover:text-white md:w-[250px] xl:w-[274px] 2xl:h-[81px] 2xl:text-[26px]'>
                        More About Us
                        <ArrowRightIcon />
                    </Button>
                </article>
            </section>
            <section className='grid grid-rows-[auto_auto] gap-10 p-4 md:grid-cols-[2fr_1fr] md:grid-rows-1 2xl:grid-rows-1'>
                <div className='relative flex h-[280px] items-start rounded-xl bg-arylide-yellow/10 p-4 md:items-center 2xl:h-[324px] 2xl:p-8 2xl:px-14'>
                    <h2 className='max-w-[10ch] text-[36px] font-semibold leading-none md:max-w-[12ch] 2xl:text-[64px] 3xl:max-w-[15ch]'>
                        Start your business account today!
                    </h2>
                    <img
                        src={HomeHero3}
                        alt='hero img'
                        className='absolute right-6 top-[90px] h-[279px] w-[208px] object-contain xl:right-8 2xl:top-7 2xl:h-auto 2xl:w-auto'
                    />
                </div>
                <div className='flex flex-col justify-center gap-y-4 rounded-xl bg-jasper-orange/10 p-8 2xl:gap-y-8'>
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
            <h1 className='text-[25px] font-bold leading-none text-metalic-blue md:text-[30px] 2xl:text-[50px]'>
                {title}
            </h1>
            <p className='text-[15px] text-black/90 md:text-[20px] 2xl:text-[24px]'>
                {description}
            </p>
        </div>
    );
}

export default Home;
