import React, { ComponentPropsWithoutRef, useState } from 'react';
import {
    IconProps,
    LinkedInIcon,
    Map,
    TwitterIcon,
    FacebookIcon,
    InstagramIcon,
} from '@/assets/central/landing-page';

function Contact() {
    return (
        <div className='3xl:max-w-[1556px] mx-auto grid w-full gap-y-10 xl:max-w-[1024px]'>
            <section className='3xl:gap-20 grid items-center gap-10 p-4 md:grid-cols-2'>
                <div>
                    <h1 className='3xl:text-[64px] text-[32px] font-bold text-black/90 md:text-[40px]'>
                        Our <span className='text-metalic-blue'>Partners</span>
                    </h1>
                    <p className='3xl:text-[32px] text-left text-[15px] text-black/90 md:text-[24px]'>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor incididunt ut labore et dolore
                        magna aliqua.
                    </p>
                </div>
                <div className='grid grid-cols-3 gap-4'>
                    <div className='3xl:h-[200px] 3xl:w-[200px] h-[100px] w-[100px] rounded-lg bg-metalic-blue'></div>
                    <div className='3xl:h-[200px] 3xl:w-[200px] h-[100px] w-[100px] rounded-lg bg-metalic-blue'></div>
                    <div className='3xl:h-[200px] 3xl:w-[200px] h-[100px] w-[100px] rounded-lg bg-metalic-blue'></div>
                    <div className='3xl:h-[200px] 3xl:w-[200px] h-[100px] w-[100px] rounded-lg bg-metalic-blue'></div>
                    <div className='3xl:h-[200px] 3xl:w-[200px] h-[100px] w-[100px] rounded-lg bg-metalic-blue'></div>
                    <div className='3xl:h-[200px] 3xl:w-[200px] h-[100px] w-[100px] rounded-lg bg-metalic-blue'></div>
                </div>
            </section>
            <section className='3xl:max-h-[699px] relative my-40 grid place-items-center items-center md:h-[430px]'>
                <img
                    src={Map}
                    alt='map logo'
                    className='absolute inset-0 hidden h-full w-full rounded-lg object-cover md:block'
                />
                <ContactForm />
                <div className='absolute right-0 hidden h-full w-[100px] items-center justify-center gap-8 bg-white bg-opacity-70 md:flex md:flex-col'>
                    <IconLink icon={FacebookIcon} href='#' />
                    <IconLink icon={InstagramIcon} href='#' />
                    <IconLink icon={LinkedInIcon} href='#' />
                    <IconLink icon={TwitterIcon} href='#' />
                </div>
            </section>
        </div>
    );
}

type IconLinkProps = {
    icon: React.FC<IconProps>;
} & ComponentPropsWithoutRef<'a'>;
function IconLink({ icon: Icon, ...props }: IconLinkProps) {
    const [mouseEntered, setMouseEntered] = useState(false);
    const [defaultColor, hoveredColor] = ['rgba(46, 52, 54, .5)', '#185ADC'];

    return (
        <a
            onMouseEnter={() => setMouseEntered(true)}
            onMouseLeave={() => setMouseEntered(false)}
            className='cursor-pointer'
            {...props}
        >
            <Icon color={mouseEntered ? hoveredColor : defaultColor} />
        </a>
    );
}

function ContactForm() {
    return (
        <form className='3xl:h-[1043px] 3xl:max-w-[671px] 3xl:p-8 grid h-max w-full gap-4 bg-metalic-blue p-8 md:absolute md:right-[100px] md:min-h-[641px] md:max-w-[410px] md:gap-y-4 md:rounded-2xl md:p-6'>
            <div className='text-white'>
                <h2 className='3xl:text-[46px] text-[32px] font-bold'>
                    Get in touch
                </h2>
                <p className='3xl:text-[24px] text-sm font-medium'>
                    Have a question? Send us a message below.
                </p>
            </div>
            <div className='grid grid-cols-2 gap-4'>
                <FormInfo
                    title='Address'
                    description='Street Name, City Name'
                />
                <FormInfo title='Phone' description='(044) 123 4567' />
                <FormInfo title='Email' description='info@email.com' />
            </div>
            <div className='3xl:space-y-8 space-y-4'>
                <FormInput placeholder='Name' />
                <FormInput placeholder='Email' />
                <FormInput placeholder='Phone Number' />
                <FormTextArea placeholder='Message' rows={5} />
            </div>
            <div className='space-y-4'>
                <button
                    type='button'
                    className='3xl:h-[66px] 3xl:text-[22px] h-[43px] w-full rounded-lg bg-white text-sm font-semibold text-metalic-blue'
                >
                    Send Message
                </button>
                <img
                    src={Map}
                    alt='map logo'
                    className='h-[130px] w-full rounded-lg object-cover md:hidden'
                />
            </div>
        </form>
    );
}

type FormInfoProps = {
    title: string;
    description: string;
};

function FormInfo({ title, description }: FormInfoProps) {
    return (
        <div className='text-white'>
            <h3 className='text-sm font-bold uppercase'>{title}</h3>
            <p className='text-sm'>{description}</p>
        </div>
    );
}

function FormInput(props: ComponentPropsWithoutRef<'input'>) {
    return (
        <input
            className='3xl:text-2xl w-full border-b border-white bg-transparent p-4 text-sm text-white outline-none placeholder:text-white'
            {...props}
        />
    );
}

function FormTextArea(props: ComponentPropsWithoutRef<'textarea'>) {
    return (
        <textarea
            className='3xl:text-2xl w-full border-b border-white bg-transparent p-4 text-sm text-white outline-none placeholder:text-white'
            {...props}
        />
    );
}

export default Contact;
