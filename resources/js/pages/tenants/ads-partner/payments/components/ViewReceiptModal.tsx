import { XIcon } from 'lucide-react';
import { PropsWithChildren } from 'react';
import { Dialog, DialogContent, DialogHeader } from '@/components/ui/dialog';

type ViewReceiptModalProps = {
    isOpen: boolean;
    onClose: () => void;
    id: number;
};

function ViewReceiptModal({ isOpen, onClose, id }: ViewReceiptModalProps) {
    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className='h-[615px] max-w-[401px]'>
                <DialogHeader>
                    <button className='ml-auto text-gray-500'>
                        <XIcon />
                    </button>
                </DialogHeader>
                <div className='mx-auto space-y-4'>
                    <div className='h-[420px] w-[300px] bg-metalic-blue'></div>
                    <div className='space-y-2'>
                        <InfoItem value='000-000-000'>Invoice No.:</InfoItem>
                        <InfoItem value=' March 08, 2023'>
                            Date received:
                        </InfoItem>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}

type InfoItemProps = { value: string } & PropsWithChildren;
function InfoItem({ value, children }: InfoItemProps) {
    return (
        <div className='flex items-center gap-4'>
            <p className='text-sm font-medium text-black/50'>{children}</p>
            <p className='text-sm'>{value}</p>
        </div>
    );
}

export default ViewReceiptModal;
