import {
    FacebookImg,
    InstagramImg,
    LinkedInImg,
    TwitterImg,
} from '@/assets/central/landing-page';
import RenturoTextLogoWhite from '@/assets/logo/RenturoLogoWhite.png';
import { ComponentPropsWithoutRef } from 'react';

function Footer() {
    return (
        <footer className='mt-40 rounded-tl-[150px] rounded-tr-[150px] bg-metalic-blue p-8 text-white'>
            <div className='mx-auto grid max-w-[1556px] grid-cols-4 justify-items-center gap-8 px-20'>
                <div className='space-y-8'>
                    <img src={RenturoTextLogoWhite} alt='app logo' />
                    <div className='space-y-4'>
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
                <div className='space-y-4'>
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
