import { ReactNode } from 'react';
import CreatePromotionLayout from './components/CreatePromotionLayout';
import CreatePromotionForm from './components/CreatePromotionForm';
import { ScrollArea } from '@/components/ui/scroll-area';
import PromotionPreview from './components/PromotionPreview';

function CreatePromotion() {
    return (
        <>
            <ScrollArea>
                <CreatePromotionForm />
            </ScrollArea>
            <ScrollArea>
                <PromotionPreview />
            </ScrollArea>
        </>
    );
}

CreatePromotion.layout = (page: ReactNode) => (
    <CreatePromotionLayout>{page}</CreatePromotionLayout>
);

export default CreatePromotion;
