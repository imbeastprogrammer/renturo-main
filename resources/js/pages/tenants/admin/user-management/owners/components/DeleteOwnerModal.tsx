import _ from 'lodash';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

import { DeleteWarningIcon } from '@/assets/tenant';
import useOwnerToast from '@/hooks/useOwnerToast';

type DeleteModalProps = {
    isOpen: boolean;
    onClose: () => void;
    id: number;
};
function DeleteOwnerModal({ isOpen, onClose, id }: DeleteModalProps) {
    const toast = useOwnerToast();

    const handleDelete = () =>
        router.delete(`/admin/users/${id}`, {
            onSuccess: () => {
                toast.success({
                    description: 'The owner has been deleted from the system.',
                });
                onClose();
            },
            onError: (errors) =>
                toast.success({ description: _.valuesIn(errors)[0] }),
        });

    return (
        <Dialog open={isOpen} onOpenChange={() => onClose()}>
            <DialogContent className='max-w-[386px] space-y-2 font-outfit'>
                <img
                    src={DeleteWarningIcon}
                    alt='Delete warning icon'
                    className='mx-auto h-[58px] w-[58px] object-contain'
                />
                <DialogHeader>
                    <DialogTitle className='text-center text-[20px] font-semibold'>
                        Are you sure?
                    </DialogTitle>
                    <DialogDescription className='text-center text-base font-thin text-black/50'>
                        This action will remove the user and all of their
                        associated data from the system.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter className='w-full'>
                    <div className='flex w-full flex-col items-center gap-2'>
                        <Button
                            variant='destructive'
                            className='h-[25px] w-[217px]'
                            onClick={handleDelete}
                        >
                            Delete User
                        </Button>
                        <Button
                            variant='outline'
                            className='h-[25px] w-[217px]'
                            onClick={onClose}
                        >
                            Cancel
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

export default DeleteOwnerModal;
