import { z } from 'zod';
import { PropsWithChildren } from 'react';
import { useForm } from 'react-hook-form';
import { Form } from '@/components/ui/form';
import { Label } from '@/components/ui/label';

import {
    FormInput,
    FormTextAreaInput,
    FormRadioInput,
    FormDurationSelector,
    FormListingSelector,
    FormRangeInput,
    FormLocationPicker,
    FormBudgetPicker,
    FormPaymentMethodSelection,
    FormGenderPicker,
    FormProofOfPaymentPicker,
} from './elements';
import formatCurrency from '@/lib/formatCurrency';
import { Button } from '@/components/ui/button';
import FormAssetList from './elements/FormAssetList';
import FormAdsButtonPicker from './elements/FormAdsButtonPicker';

const createPromotionSchema = z.object({
    name: z.string(),
    message: z.string(),
    ads_type: z.string(),
    ads_asset: z.array(
        z
            .string()
            .optional()
            .or(
                z
                    .custom<File>()
                    .refine(
                        (file) =>
                            !file ||
                            (!!file && file.type?.startsWith('image')) ||
                            file.type?.startsWith('video'),
                        {
                            message:
                                'Only images or video are allowed to be sent.',
                        },
                    ),
            ),
    ),
    custom_button: z.string(),
    goal: z.string(),
    listing_id: z.string(),
    target_audience: z.string(),
    target_gender: z.string(),
    age_range: z.array(z.number()).max(2).min(2),
    target_locations: z.array(z.string()),
    duration: z.array(z.date()).min(2).max(2),
    badget: z.string(),
    payment_method: z.string(),
    proof_of_payment: z
        .string()
        .optional()
        .or(
            z
                .custom<File>()
                .refine(
                    (file) =>
                        !file || (!!file && file.size <= 10 * 1024 * 1024),
                    {
                        message:
                            'The profile picture must be a maximum of 10MB.',
                    },
                )
                .refine(
                    (file) =>
                        !file || (!!file && file.type?.startsWith('image')),
                    {
                        message: 'Only images are allowed to be sent.',
                    },
                ),
        ),
});

type CreatePromotionFormFields = z.infer<typeof createPromotionSchema>;
const defaultValues: CreatePromotionFormFields = {
    name: '',
    message: '',
    ads_type: 'single',
    ads_asset: [],
    custom_button: 'learn_more',
    goal: '',
    listing_id: '',
    target_audience: '',
    target_gender: '',
    age_range: [18, 25],
    target_locations: [],
    duration: [],
    badget: '',
    payment_method: '',
    proof_of_payment: '',
};

const goalsSelection = [
    {
        label: 'Increase brand awareness',
        value: 'increase-brand-awareness',
        description:
            'Show your brand to more people and make them more familiar with your products or services.',
    },
    {
        label: 'Generate leads',
        value: 'generate-leads',
        description:
            'Collect contact information from potential customers so you can follow up with them and nurture them into paying customers.',
    },
    {
        label: 'Drive traffic into your property',
        value: 'drive-traffic-to-your-property',
        description:
            'Get more people to visit your website so they can learn more about your products or services and make a purchase.',
    },
    {
        label: 'Boost sales',
        value: 'boost-sales',
        description: 'Increase the number of products or services you sell.',
    },
    {
        label: 'Increase engagement',
        value: 'increase-engagement',
        description:
            'Get people to spend more time on your website and interact with you content.',
    },
    {
        label: 'Improve customer loyalty',
        value: 'improve-customer-loyalty',
        description:
            'Encourage existing customers to continue doing business with you.',
    },
];

const listings = [
    {
        value: '1',
        label: "Joshua's Basketvall court",
        image: 'https://images.pexels.com/photos/2277981/pexels-photo-2277981.jpeg?auto=compress&cs=tinysrgb&w=800',
    },
    {
        value: '2',
        label: "Joshua's swimming pool",
        image: 'https://images.pexels.com/photos/97047/pexels-photo-97047.jpeg?auto=compress&cs=tinysrgb&w=800',
    },
    {
        value: '3',
        label: "Joshua's computer shop for tournament",
        image: 'https://images.pexels.com/photos/9071770/pexels-photo-9071770.jpeg?auto=compress&cs=tinysrgb&w=800',
    },
];

const targetAudiences = [
    {
        label: 'Manually selected autdience',
        value: 'manually-selected-audience',
    },
    { label: 'Your custom audiences', value: 'your-custom-audiences' },
];

const paymentMethods = [
    {
        label: 'Gcash',
        value: 'gcash',
        details: { name: 'Juan Dela Cruz', 'Account Number': '0998 123 4567' },
    },
    {
        label: 'Bank Transfer',
        value: 'bank-transfer',
        details: {
            name: 'Juan Dela Cruz',
            'Account Number': '0998 123 4567',
        },
    },
];

const gender = [
    { label: 'All', value: 'all' },
    { label: 'Men', value: 'men' },
    { label: 'Women', value: 'women' },
];

const adsType = [
    {
        label: 'Single (Image or Video)',
        value: 'single',
    },
    { label: 'Carousel', value: 'multiple' },
];

const customButtons = [
    { label: 'Learn More', value: 'learn_more', href: '' },
    { label: 'Book Now', value: 'book_now', href: '' },
    { label: 'Get Started', value: 'get_started', href: '' },
    { label: 'Request a Quote', value: 'request_a_quote', href: '' },
    { label: 'Discover Now', value: 'discover_now', href: '' },
    { label: 'Explore More', value: 'explore_more', href: '' },
];

function CreatePromotionForm() {
    const form = useForm<CreatePromotionFormFields>({ defaultValues });
    const selectedAdsType = form.watch('ads_type') as 'single' | 'multiple';

    const handleSubmit = form.handleSubmit(() => {});

    return (
        <Form {...form}>
            <form onSubmit={handleSubmit} className='space-y-8 p-6'>
                <PromotionItemContainer title='Promotion Info'>
                    <FormInput
                        name='name'
                        control={form.control}
                        label='Promotion Name'
                    />
                    <FormTextAreaInput
                        name='Message'
                        control={form.control}
                        label='Message'
                        description="Want to grab attention with your promoted post? Customize it with a message or caption that tells users more about what you're promoting."
                    />
                </PromotionItemContainer>
                <PromotionItemContainer title='Ad Setup'>
                    <FormRadioInput
                        name='ads_type'
                        control={form.control}
                        label='Format'
                        data={adsType}
                        description='Customize your promotion structure to meet your specific needs.'
                    />
                    <FormAssetList
                        name='ads_asset'
                        control={form.control}
                        type={selectedAdsType}
                    />
                    <FormAdsButtonPicker
                        name='custom_button'
                        label='Custom Button'
                        control={form.control}
                        data={customButtons}
                    />
                </PromotionItemContainer>
                <PromotionItemContainer title='Promote Your Listing'>
                    <div className='space-y-2'>
                        <Label className='text-xl'>Listing</Label>
                        <p className='font-medium text-black/50'>
                            Choose the listing you want to boost and let us help
                            you achieve your goals.
                        </p>
                    </div>
                    <FormListingSelector
                        label='Your Listings'
                        control={form.control}
                        name='listing_id'
                        data={listings}
                    />
                </PromotionItemContainer>
                <PromotionItemContainer title='Prmotion Goal'>
                    <FormRadioInput
                        name='goal'
                        label='Goal'
                        description='What impact do you want this promotion to have?'
                        control={form.control}
                        data={goalsSelection}
                    />
                </PromotionItemContainer>
                <PromotionItemContainer title='Promotion Audience'>
                    <FormRadioInput
                        name='target_audience'
                        label='Choose who you want to see your promotion'
                        description='Make your promotion more effective by targeting it to specific locations, ages, genders, and interests.'
                        control={form.control}
                        data={targetAudiences}
                    />
                    <FormGenderPicker
                        name='target-gender'
                        label='Gender'
                        control={form.control}
                        data={gender}
                    />
                    <FormRangeInput
                        name='age_range'
                        control={form.control}
                        label='Age'
                        min={0}
                        max={40}
                    />
                    <div className='space-y-2'>
                        <Label className='text-xl'>Location</Label>
                        <p className='font-medium text-black/50'>
                            Reach people who are likely to be interested in your
                            promotion because they are already in or have
                            recently been in this location.
                        </p>
                    </div>
                    <FormLocationPicker />
                </PromotionItemContainer>
                <PromotionItemContainer title='Promotion Budget'>
                    <FormDurationSelector
                        label='Duration'
                        control={form.control}
                        name='duration'
                    />
                    <FormBudgetPicker
                        name='budget'
                        control={form.control}
                        label='Total Budget'
                        min={100}
                        max={10_000}
                    />
                </PromotionItemContainer>
                <PromotionItemContainer title='Payment Information'>
                    <div>
                        <Label className='text-xl'>Amount Due</Label>
                        <p className='text-lg font-medium text-metalic-blue'>
                            {formatCurrency(1_500)}
                        </p>
                    </div>
                    <FormPaymentMethodSelection
                        label='Payment Method'
                        control={form.control}
                        name='payment_method'
                        data={paymentMethods}
                    />
                    <FormProofOfPaymentPicker
                        label='Proof of Payment'
                        name='proof_of_payment'
                        control={form.control}
                    />
                </PromotionItemContainer>
                <div className='flex justify-end gap-4'>
                    <Button
                        variant='outline'
                        className='border-metalic-blue text-metalic-blue hover:bg-metalic-blue/5 hover:text-metalic-blue'
                    >
                        Save
                    </Button>
                    <Button className='bg-metalic-blue hover:bg-metalic-blue/90'>
                        Book Post Now
                    </Button>
                </div>
            </form>
        </Form>
    );
}

type PromotionItemContainer = {
    title: string;
} & PropsWithChildren;

function PromotionItemContainer({ title, children }: PromotionItemContainer) {
    return (
        <section className='space-y-4'>
            <h1 className='text-lg font-semibold text-black/50'>{title}</h1>
            <div className='space-y-4 rounded-lg border bg-white p-4'>
                {children}
            </div>
        </section>
    );
}

export default CreatePromotionForm;
