import {
    AppStore,
    GooglePlay,
    PhoneMockup,
    Qrcode,
} from '@/assets/central/landing-page';

function Download() {
    return (
        <div className='p-4'>
            <section className='mx-auto flex h-[428px] max-w-[1556px] items-center justify-center gap-20 rounded-3xl bg-metalic-blue'>
                <div>
                    <img src={PhoneMockup} alt='phone mockup' />
                </div>
                <div className='space-y-8'>
                    <h1 className='max-w-[10ch] text-[46px] font-semibold leading-none text-white'>
                        Download our MobileApp
                    </h1>
                    <div className='flex gap-4'>
                        <a href='#'>
                            <img
                                src={AppStore}
                                alt='app store download button'
                            />
                        </a>
                        <a href='#'>
                            <img
                                src={GooglePlay}
                                alt='google play download button'
                            />
                        </a>
                    </div>
                </div>
                <div className='space-y-4'>
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
