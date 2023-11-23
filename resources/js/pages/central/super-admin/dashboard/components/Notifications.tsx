import { ReactNode } from 'react';
import { Separator } from '@/components/ui/separator';
import { BiSolidNotification } from 'react-icons/bi';
import { HiBell } from 'react-icons/hi';
import { FaArrowRight } from 'react-icons/fa';

function Notifications() {
    return (
        <div className='h-full w-full rounded-lg bg-white p-4'>
            <div className='flex items-center justify-between'>
                <div className='flex items-center gap-4'>
                    <HiBell className='h-[30px] w-[30px] text-black/80' />
                    <h1 className='text-lg text-black/80'>Notifications</h1>
                </div>
                <FaArrowRight className='h-[30px] w-[30px] text-black/80' />
            </div>
            <Separator className='my-2' />
            <div className='space-y-2'>
                <NotificationItem>
                    A tenant has exceeded their usage limit.
                </NotificationItem>
                <NotificationItem>
                    A tenant has requested to cancel their plan.
                </NotificationItem>
                <NotificationItem>
                    A tenant's account has been suspended for non-payment.
                </NotificationItem>
                <NotificationItem>
                    A new support ticket has been created.
                </NotificationItem>
                <NotificationItem>
                    A tenant's payment has been declined.
                </NotificationItem>
                <NotificationItem>A tenant's invoice is due.</NotificationItem>
                <NotificationItem>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed
                    do eiusmod tempor incididunt ut labore et dolore magna
                    aliqua.
                </NotificationItem>
                <NotificationItem>
                    auctor urna nunc id cursus metus aliquam eleifend mi in
                    nulla posuere sollicitudin aliquam ultrices sagittis orci a
                    scelerisque purus
                </NotificationItem>
                <p className='cursor-pointer text-[15px] font-medium text-picton-blue hover:underline'>
                    30 unread notifications
                </p>
            </div>
        </div>
    );
}

type NotificationItemProps = { children: ReactNode };
function NotificationItem({ children }: NotificationItemProps) {
    return (
        <div className='flex items-center gap-4 text-[15px] text-black/50'>
            <BiSolidNotification
                alt='Notification icon item'
                className='h-[25px] w-[25px] flex-shrink-0'
            />
            {children}
        </div>
    );
}

export default Notifications;
