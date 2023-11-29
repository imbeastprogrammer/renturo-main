import {
    AppStore,
    GooglePlay,
    PhoneMockup,
    Qrcode,
} from '@/assets/central/landing-page';

function Download() {
    return (
        <div className='p-4 py-20 pl-0 md:pl-4'>
            <section className='mx-auto flex max-h-[200px] w-full max-w-[1556px] items-center justify-center gap-4 rounded-br-3xl rounded-tr-3xl bg-metalic-blue p-8 md:max-h-[260px] md:gap-8 md:rounded-3xl'>
                <div>
                    <img
                        src={PhoneMockup}
                        alt='phone mockup'
                        className='h-full max-h-[260px] w-full max-w-[130px] object-contain md:max-h-[473px] md:max-w-[236px]'
                    />
                </div>
                <div className='space-y-8'>
                    <h1 className='max-w-[10ch] text-[22px] font-semibold leading-none text-white xl:text-[46px]'>
                        Download our Mobile App
                    </h1>
                    <div className='flex flex-col gap-2'>
                        <a href='#'>
                            <img
                                src={AppStore}
                                alt='app store download button'
                                className='h-[28px] w-[93px]'
                            />
                        </a>
                        <a href='#'>
                            <img
                                src={GooglePlay}
                                alt='google play download button'
                                className='h-[28px] w-[93px]'
                            />
                        </a>
                    </div>
                </div>
                <div className='hidden space-y-4 md:block'>
                    <p className='text-lg font-medium text-white'>
                        Scan QR to download
                    </p>
                    <img src={Qrcode} alt='' />
                </div>
            </section>
        </div>
    );
}

export default Download;
