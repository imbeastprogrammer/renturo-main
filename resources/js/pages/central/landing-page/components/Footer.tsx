import { ComponentPropsWithoutRef } from 'react';
import {
    FacebookImg,
    InstagramImg,
    LinkedInImg,
    TwitterImg,
} from '@/assets/central/landing-page';
import RenturoTextLogoWhite from '@/assets/logo/RenturoLogoWhite.png';

function Footer() {
    return (
        <footer className='rounded-tl-3xl rounded-tr-3xl bg-metalic-blue p-8 text-white xl:rounded-tl-[150px] xl:rounded-tr-[150px]'>
            <div className='3xl:max-w-screen-2xl mx-auto grid gap-8 md:grid-cols-4 md:grid-rows-1 md:justify-items-center xl:max-w-screen-lg 2xl:max-w-screen-xl'>
                <div className='space-y-8'>
                    <div className='flex items-center justify-between gap-4'>
                        <img
                            src={RenturoTextLogoWhite}
                            alt='app logo'
                            className='h-[33px] md:h-auto'
                        />
                        <div className='flex gap-2 md:hidden'>
                            <SocialIconLink icon={FacebookImg} href='#' />
                            <SocialIconLink icon={InstagramImg} href='#' />
                            <SocialIconLink icon={LinkedInImg} href='#' />
                            <SocialIconLink icon={TwitterImg} href='#' />
                        </div>
                    </div>
                    <div className='hidden space-y-4 md:block'>
                        <div>
                            © 2023 KabootekPh Inc. <br /> All Rights Reserved
                        </div>
                        <div>Terms & Condition | Privacy Policy</div>
                    </div>
                </div>
                <div className='space-y-4'>
                    <h4 className='text-xl font-bold'>About Renturo</h4>
                    <div>
                        <a href='#'>About Us</a>
                    </div>
                </div>
                <div className='space-y-4'>
                    <h4 className='text-xl font-bold'>Help & Information</h4>
                    <div className='grid gap-y-2'>
                        <div>FAQs</div>
                        <div>Contact Us</div>
                    </div>
                </div>
                <div className='hidden space-y-4 md:block'>
                    <h4 className='text-xl font-bold'>Get in Touch</h4>
                    <div className='grid gap-2'>
                        <div>
                            Questions of feedback? We’d love to hear from you.
                        </div>
                        <span className='font-semibold'>email@gmail.com</span>
                    </div>
                    <div className='flex gap-2'>
                        <SocialIconLink icon={FacebookImg} href='#' />
                        <SocialIconLink icon={InstagramImg} href='#' />
                        <SocialIconLink icon={LinkedInImg} href='#' />
                        <SocialIconLink icon={TwitterImg} href='#' />
                    </div>
                </div>
            </div>
        </footer>
    );
}

type SocialIconLinkProps = {
    icon: string;
} & ComponentPropsWithoutRef<'a'>;

function SocialIconLink({ icon, ...props }: SocialIconLinkProps) {
    return (
        <a
            className='grid h-[30px] w-[30px] place-items-center rounded-md bg-white shadow-xl'
            {...props}
        >
            <img
                src={icon}
                alt='social icon'
                className='h-[20px] w-[20px] object-contain'
            />
        </a>
    );
}

export default Footer;
