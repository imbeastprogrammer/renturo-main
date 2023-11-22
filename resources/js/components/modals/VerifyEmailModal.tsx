import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import PinInput from '../PinInput';
import { VerifyEmailIcon } from '@/assets/tenant/modal';

export function VerfiyEmailModal() {
    return (
        <Dialog open>
            <DialogContent className='w-full max-w-[430px]'>
                <div className='mx-auto w-full max-w-[346px] space-y-6'>
                    <img
                        src={VerifyEmailIcon}
                        alt='verify email icon'
                        className='mx-auto'
                    />
                    <DialogHeader>
                        <DialogTitle className='text-center text-xl font-semibold'>
                            Verify your email
                        </DialogTitle>
                        <DialogDescription className='text-center text-base text-black/50'>
                            Please enter the 4-digit code sent to
                            email@email.com
                        </DialogDescription>
                    </DialogHeader>
                    <div>
                        <PinInput
                            length={4}
                            value=''
                            onChange={() => {}}
                            secret={false}
                        />
                    </div>

                    <DialogFooter className='w-full'>
                        <div className='grid w-full space-y-2'>
                            <Button
                                type='submit'
                                className='w-full bg-[#84C58A] text-[14px] font-medium hover:bg-[#84C58A]/90'
                            >
                                Confirm
                            </Button>
                            <Button
                                type='submit'
                                variant='link'
                                className='text-metalic-blue underline'
                            >
                                Change Email
                            </Button>
                        </div>
                    </DialogFooter>
                </div>
            </DialogContent>
        </Dialog>
    );
}
