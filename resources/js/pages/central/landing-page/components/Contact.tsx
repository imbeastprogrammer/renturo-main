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
        <div className='mx-auto w-full max-w-[1556px] space-y-10'>
            <section className='grid items-center gap-20 p-4 xl:grid-cols-2'>
                <div>
                    <h1 className='text-[32px] font-bold text-black/90 xl:text-[64px]'>
                        Our <span className='text-metalic-blue'>Partners</span>
                    </h1>
                    <p className='text-left text-[15px] text-black/90 xl:text-[32px]'>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor incididunt ut labore et dolore
                        magna aliqua.
                    </p>
                </div>
                {/* <div className='flex gap-4'>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                </div> */}
            </section>
            <section className='grid w-full items-center xl:grid-cols-[1fr_671px_150px]'>
                <img
                    src={Map}
                    alt='map logo'
                    className='hidden h-[699px] w-full rounded-s-lg object-cover object-right-top'
                />
                <ContactForm />
                <div className='hidden justify-items-center gap-8 xl:grid'>
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
function IconLink(props: IconLinkProps) {
    const [mouseEntered, setMouseEntered] = useState(false);
    const [defaultColor, hoveredColor] = ['rgba(46, 52, 54, .5)', '#185ADC'];

    return (
        <a
            onMouseEnter={() => setMouseEntered(true)}
            onMouseLeave={() => setMouseEntered(false)}
            className='cursor-pointer'
            {...props}
        >
            <props.icon color={mouseEntered ? hoveredColor : defaultColor} />
        </a>
    );
}

function ContactForm() {
    return (
        <form className='grid h-max gap-4 bg-metalic-blue p-8 xl:h-[1043px] xl:gap-y-4 xl:rounded-2xl xl:p-8'>
            <div className='text-white'>
                <h2 className='text-[32px] font-bold xl:text-[46px]'>
                    Get in touch
                </h2>
                <p className='text-sm font-medium xl:text-[24px]'>
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
            <div className='space-y-4 xl:space-y-8'>
                <FormInput placeholder='Name' />
                <FormInput placeholder='Email' />
                <FormInput placeholder='Phone Number' />
                <FormTextArea placeholder='Message' rows={5} />
            </div>
            <div className='space-y-4'>
                <button
                    type='button'
                    className='h-[43px] w-full rounded-lg bg-white text-sm font-semibold text-metalic-blue xl:h-[66px] xl:text-[22px]'
                >
                    Send Message
                </button>
                <img
                    src={Map}
                    alt='map logo'
                    className='h-[130px] w-full rounded-lg object-cover xl:hidden'
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
            className='w-full border-b border-white bg-transparent p-4 text-sm text-white outline-none placeholder:text-white xl:text-2xl'
            {...props}
        />
    );
}

function FormTextArea(props: ComponentPropsWithoutRef<'textarea'>) {
    return (
        <textarea
            className='w-full border-b border-white bg-transparent p-4 text-sm text-white outline-none placeholder:text-white xl:text-2xl'
            {...props}
        />
    );
}

export default Contact;
