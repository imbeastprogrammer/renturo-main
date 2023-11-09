import _ from 'lodash';
import { z } from 'zod';
import { router } from '@inertiajs/react';
import { useToast } from '@/components/ui/use-toast';
import { ReactNode } from 'react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { Button } from '@/components/ui/button';
import { Form } from '@/components/ui/form';

import { ErrorIcon } from '@/assets/central';
import FormInput from '@/components/super-admin-form-elements/FormInput';
import FormSelect from '@/components/super-admin-form-elements/FormSelect';
import { UsagePlansMap } from '../usage-plans';

const createTenantFormSchema = z.object({
    domain: z.string().nonempty(),
    usage_plan: z.string().optional(),
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    username: z.string().optional(),
    email: z.string().email().nonempty(),
    mobile_no: z.string().nonempty(),
});

type CreateTenantFormFields = z.infer<typeof createTenantFormSchema>;
const defaultValues: CreateTenantFormFields = {
    domain: '',
    usage_plan: '',
    first_name: '',
    last_name: '',
    username: '',
    email: '',
    mobile_no: '',
};

function CreateTenantForm() {
    const { toast } = useToast();
    const form = useForm<CreateTenantFormFields>({
        defaultValues,
        resolver: zodResolver(createTenantFormSchema),
    });

    const selectedUsagePlan = UsagePlansMap[form.watch('usage_plan') || ''];

    const hasErrors = Object.keys(form.formState.errors).length > 0;

    const onSubmit = form.handleSubmit(({ domain, usage_plan, ...values }) => {
        router.post(
            '/super-admin/tenants',
            { ...values, name: domain, plan_type: usage_plan },
            {
                onSuccess: () => {
                    toast({
                        title: 'Success',
                        description:
                            'The new tenant has been added to the system.',
                        style: {
                            marginBottom: '1rem',
                            transform: 'translateX(-1rem)',
                        },
                        variant: 'default',
                    });
                    router.visit('/super-admin/site-management/tenants', {
                        replace: true,
                    });
                },
                onError: (error) =>
                    toast({
                        title: 'Error',
                        description:
                            _.valuesIn(error)[0] ||
                            'Something went wrong, Please try again later.',
                        style: {
                            marginBottom: '1rem',
                            transform: 'translateX(-1rem)',
                        },
                        variant: 'destructive',
                    }),
            },
        );
    });

    return (
        <Form {...form}>
            <form className='flex flex-col gap-2 p-6' onSubmit={onSubmit}>
                <h1 className='text-base text-[#2E3436]/50'>
                    To register a new organization, please enter your
                    organization's name, contact information, and desired domain
                    name.
                </h1>
                <div className='space-y-4'>
                    <SectionTitle>Domain Information</SectionTitle>
                    <div className='flex items-center gap-4'>
                        <div className='min-w-[760px] max-w-[760px]'>
                            <FormInput
                                name='domain'
                                label='Domain'
                                placeholder='Domain'
                                control={form.control}
                            />
                        </div>
                        <div className='text-base text-[#2E3436]/50'>
                            Register a unique domain name for your organization,
                            in the format “example.com”.
                        </div>
                    </div>
                </div>
                <div className='space-y-4'>
                    <SectionTitle>Usage Plan Information</SectionTitle>
                    <div>
                        <FormSelect
                            name='usage_plan'
                            label='Usage Plan'
                            control={form.control}
                            data={[
                                {
                                    label: 'Demo',
                                    value: 'demo',
                                },
                                {
                                    label: 'Starter Plan',
                                    value: 'starter_plan',
                                },
                                {
                                    label: 'Professional Plan',
                                    value: 'professional_plan',
                                },
                                {
                                    label: 'Enterprise Plan',
                                    value: 'enterprise_plan',
                                },
                                {
                                    label: 'Custom Plan',
                                    value: 'custom_plan',
                                },
                            ]}
                        />
                        {selectedUsagePlan && (
                            <div className='ml-[200px] mt-2'>
                                <p className='mb-4 text-base text-[#2E3436]/50'>
                                    {selectedUsagePlan.description}
                                </p>
                                <ul className='ml-6 list-disc text-base text-[#2E3436]/50'>
                                    {selectedUsagePlan.inclusion.map(
                                        (inclusion, idx) => (
                                            <li key={idx}>{inclusion}</li>
                                        ),
                                    )}
                                </ul>
                            </div>
                        )}
                    </div>
                </div>
                <div className='space-y-4'>
                    <SectionTitle>Tenant Admin</SectionTitle>
                    <div className='max-w-[760px] space-y-4'>
                        <FormInput
                            name='first_name'
                            label='First Name'
                            placeholder='First Name'
                            control={form.control}
                        />
                        <FormInput
                            name='last_name'
                            label='Last Name'
                            placeholder='Last Name'
                            control={form.control}
                        />
                        <FormInput
                            name='username'
                            label='Username'
                            placeholder='Username'
                            control={form.control}
                        />
                    </div>
                </div>
                <div className='space-y-4'>
                    <SectionTitle>Contact Details</SectionTitle>
                    <div className='max-w-[760px] space-y-4'>
                        <FormInput
                            name='email'
                            label='Email Address'
                            placeholder='Email Address'
                            control={form.control}
                        />
                        <FormInput
                            name='mobile_no'
                            label='Mobile Number'
                            placeholder='Mobile Number'
                            control={form.control}
                        />
                    </div>
                </div>
                <div className='flex justify-end text-base'>
                    {hasErrors && (
                        <div className='flex items-center gap-2'>
                            <img src={ErrorIcon} />
                            <p>
                                Please complete all required fields before
                                submitting.
                            </p>
                        </div>
                    )}
                </div>
                <div className='flex justify-end'>
                    <Button
                        type='submit'
                        className='bg-[#84C58A] px-8 text-base font-medium hover:bg-[#84C58A]/90'
                    >
                        Create Tenant
                    </Button>
                </div>
            </form>
        </Form>
    );
}

type SectionTitleProps = { children: ReactNode };

function SectionTitle({ children }: SectionTitleProps) {
    return (
        <div className='rounded-lg bg-[#F0F0F0] px-4 py-2 text-[18px] font-semibold'>
            <h2>{children}</h2>
        </div>
    );
}

export default CreateTenantForm;
