import { useState } from 'react';
import { DeleteWarning } from '@/assets/central';
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

import useCentralToast from '@/hooks/useCentralToast';
import getSuccessMessage from '@/lib/getSuccessMessage';

type DeleteModalProps = {
    isOpen: boolean;
    onClose: () => void;
    id: number;
};
function DeleteCategoryModal({ isOpen, onClose, id }: DeleteModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const toast = useCentralToast();

    const handleDelete = () => {
        router.delete(`/admin/form/${id}`, {
            onBefore: () => setIsLoading(true),
            onFinish: () => setIsLoading(false),
            onSuccess: (data) => {
                onClose();
                toast.success({
                    description: getSuccessMessage(data),
                });
            },
            onError: (error) => {
                onClose();
                toast.error({
                    description: Object.keys(error)[0],
                });
            },
        });
    };

    const handleClose = () => !isLoading && onClose();
    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className='max-w-[386px] space-y-2 font-outfit'>
                <img
                    src={DeleteWarning}
                    alt='Delete warning icon'
                    className='mx-auto h-[58px] w-[58px] object-contain'
                />
                <DialogHeader>
                    <DialogTitle className='text-center text-[20px] font-semibold'>
                        Are you sure?
                    </DialogTitle>
                    <DialogDescription className='text-center text-base font-thin text-black/50'>
                        This action will remove the form and all of their
                        associated data from the system.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter className='w-full'>
                    <div className='flex w-full flex-col items-center gap-2'>
                        <Button
                            variant='destructive'
                            className='h-[25px] w-[217px]'
                            disabled={isLoading}
                            onClick={handleDelete}
                        >
                            Delete Form
                        </Button>
                        <Button
                            variant='outline'
                            className='h-[25px] w-[217px]'
                            onClick={handleClose}
                        >
                            Cancel
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

export default DeleteCategoryModal;
