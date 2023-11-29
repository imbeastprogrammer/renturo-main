import {
    AnalyticsAndReportingIcon,
    BookAndReserveIcon,
    ListItemIcon,
} from '@/assets/central/landing-page';

function About() {
    return (
        <div className='bg-metalic-blue/10'>
            <article className='3xl:max-w-[1556px] mx-auto w-full space-y-10 p-8 xl:max-w-[1024px]'>
                <div className='text-center'>
                    <h1 className='3xl:text-[64px] text-[32px] font-bold text-black/90 md:text-[35px]'>
                        Why <span className='text-metalic-blue'>Renturo?</span>
                    </h1>
                    <p className='3xl:text-[32px] mx-auto max-w-[50ch] text-[15px] md:max-w-[40ch] md:text-[22px]'>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                        sed do eiusmod tempor incididunt ut labore et dolore
                        magna aliqua.
                    </p>
                </div>
                <div className='flex flex-col justify-center gap-8 md:flex-row'>
                    <ServiceCard
                        title='List Items'
                        description='Lorem ipsum dolor sit amet, consectetur adipiscing elit'
                        icon={ListItemIcon}
                    />
                    <ServiceCard
                        title='Book and Reserve'
                        description='Lorem ipsum dolor sit amet, consectetur adipiscing elit'
                        icon={BookAndReserveIcon}
                    />
                    <ServiceCard
                        title='Analytics and Reporting'
                        description='Lorem ipsum dolor sit amet, consectetur adipiscing elit'
                        icon={AnalyticsAndReportingIcon}
                    />
                </div>
            </article>
        </div>
    );
}

type ServiceCardProps = { title: string; description: string; icon: string };
function ServiceCard({ title, description, icon }: ServiceCardProps) {
    return (
        <div className='space-y-4 rounded-lg bg-white p-8 text-center'>
            <div className='mx-auto grid h-[70px] w-[70px] place-items-center rounded-full bg-metalic-blue'>
                <img
                    src={icon}
                    alt='service icon'
                    className='h-[35px] w-[35px] object-contain'
                />
            </div>
            <h2 className='3xl:text-[32px] text-xl font-semibold'>{title}</h2>
            <p className='3xl:text-[24px] text-[15px] text-black/90'>
                {description}
            </p>
        </div>
    );
}

export default About;
