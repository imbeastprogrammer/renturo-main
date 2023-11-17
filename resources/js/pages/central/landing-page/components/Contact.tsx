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
        <div className='mx-auto max-w-[1556px] space-y-20 p-4'>
            <section className='grid grid-cols-2 items-center gap-20'>
                <div>
                    <h1 className='text-[64px] font-bold text-black/90'>
                        Our <span className='text-metalic-blue'>Partners</span>
                    </h1>
                    <p className='text-left text-[32px] text-black/90'>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor incididunt ut labore et dolore
                        magna aliqua.
                    </p>
                </div>
                <div className='grid grid-cols-3 gap-4'>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                    <div className='h-[200px] w-[200px] rounded-lg bg-metalic-blue'></div>
                </div>
            </section>
            <section className='grid grid-cols-[1fr_671px_150px] items-center'>
                <img
                    src={Map}
                    alt='map logo'
                    className='h-[699px] w-full rounded-s-lg object-cover object-right-top'
                />
                <ContactForm />
                <div className='grid justify-items-center gap-8'>
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
        <form className='grid h-[1043px] gap-y-8 rounded-2xl bg-metalic-blue p-8'>
            <div className='text-white'>
                <h2 className='text-[46px] font-bold'>Get in touch</h2>
                <p className='text-[24px] font-medium'>
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
            <div className='space-y-8'>
                <FormInput placeholder='Name' />
                <FormInput placeholder='Email' />
                <FormInput placeholder='Phone Number' />
                <FormTextArea placeholder='Message' rows={5} />
            </div>
            <div>
                <button
                    type='button'
                    className='h-[66px] w-full rounded-lg bg-white text-[22px] font-semibold text-metalic-blue'
                >
                    Send Message
                </button>
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
            <h3 className='text-xl font-bold uppercase'>{title}</h3>
            <p className='text-xl'>{description}</p>
        </div>
    );
}

function FormInput(props: ComponentPropsWithoutRef<'input'>) {
    return (
        <input
            className='w-full border-b border-white bg-transparent p-4 text-2xl text-white outline-none placeholder:text-white'
            {...props}
        />
    );
}

function FormTextArea(props: ComponentPropsWithoutRef<'textarea'>) {
    return (
        <textarea
            className='w-full border-b border-white bg-transparent p-4 text-2xl text-white outline-none placeholder:text-white'
            {...props}
        />
    );
}

export default Contact;
