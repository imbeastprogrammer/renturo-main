import { ReactNode } from 'react';
import { Separator } from '@/components/ui/separator';
import { BiSolidNotification } from 'react-icons/bi';
import { FaRegBell } from 'react-icons/fa';

function Notifications() {
    return (
        <div className='grid h-full w-full grid-rows-[auto_auto_1fr] rounded-lg border bg-white p-4 shadow-lg'>
            <div className='flex items-center gap-4'>
                <FaRegBell className='h-[30px] w-[30px] text-metalic-blue' />
                <h1 className='text-lg font-medium'>Notifications</h1>
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
                <p className='cursor-pointer text-[15px] font-medium text-jasper-orange hover:underline'>
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
