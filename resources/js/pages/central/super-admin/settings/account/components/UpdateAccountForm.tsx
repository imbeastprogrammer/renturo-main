import _ from 'lodash';
import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Form, FormField } from '@/components/ui/form';
import FormInput from '@/components/super-admin/forms/FormInput';
import { Button } from '@/components/ui/button';
import { ErrorIcon } from '@/assets/central';
import ProfilePicturePicker from './ProfilePicturePicker';

const updateAccountSchema = z.object({
    profile_picture: z
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
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    email: z.string().nonempty(),
    contact_no: z.string().nonempty().length(11),
    address: z.string().optional(),
    city: z.string().nonempty(),
    province: z.string().nonempty(),
    country: z.string().nonempty(),
    zipcode: z.string().nonempty(),
});

type UpdateAccountFormFields = z.infer<typeof updateAccountSchema>;

const defaultValues: UpdateAccountFormFields = {
    profile_picture: '',
    first_name: '',
    last_name: '',
    email: '',
    contact_no: '',
    address: '',
    city: '',
    province: '',
    country: '',
    zipcode: '',
};

function UpdateAccountForm() {
    const form = useForm<UpdateAccountFormFields>({
        defaultValues,
        resolver: zodResolver(updateAccountSchema),
    });

    const onSubmit = form.handleSubmit(() => {});
    const hasErrors = Object.keys(form.formState.errors).length > 0;

    return (
        <Form {...form}>
            <form onSubmit={onSubmit} className='space-y-6 p-4'>
                <div>
                    <h1 className='text-xl font-medium'>General Info</h1>
                    <p className='text-black/50'>
                        Update your photo and personal details here.
                    </p>
                </div>
                <FormField
                    control={form.control}
                    name='profile_picture'
                    render={({ field }) => (
                        <ProfilePicturePicker
                            value={field.value || ''}
                            onChange={field.onChange}
                        />
                    )}
                />
                <div className='grid grid-cols-2 gap-4'>
                    <FormInput
                        control={form.control}
                        name='first_name'
                        label='First Name'
                        orientation='vertical'
                    />
                    <FormInput
                        control={form.control}
                        name='last_name'
                        label='Last Name'
                        orientation='vertical'
                    />
                    <FormInput
                        control={form.control}
                        name='email'
                        label='Email Address'
                        orientation='vertical'
                    />
                    <FormInput
                        control={form.control}
                        name='contact_no'
                        label='Contact Number'
                        orientation='vertical'
                    />
                    <div className='col-span-2'>
                        <FormInput
                            control={form.control}
                            name='address'
                            label='Address'
                            orientation='vertical'
                        />
                    </div>
                    <FormInput
                        control={form.control}
                        name='city'
                        label='City'
                        orientation='vertical'
                    />
                    <FormInput
                        control={form.control}
                        name='province'
                        label='Province'
                        orientation='vertical'
                    />
                    <FormInput
                        control={form.control}
                        name='country'
                        label='Country'
                        orientation='vertical'
                    />
                    <FormInput
                        control={form.control}
                        name='zipcode'
                        label='Zipcode'
                        orientation='vertical'
                    />
                </div>
                <div>
                    {hasErrors &&
                        _.valuesIn(form.formState.errors).map((error, i) => (
                            <div key={i} className='flex items-center gap-2'>
                                <img src={ErrorIcon} />
                                <p>{error.message}</p>
                            </div>
                        ))}
                </div>
                <div className='flex justify-end gap-4'>
                    <Button variant='outline'>Cancel</Button>
                    <Button
                        type='submit'
                        className='bg-[#84C58A] hover:bg-[#84C58A]/90'
                    >
                        Save Changes
                    </Button>
                </div>
            </form>
        </Form>
    );
}

export default UpdateAccountForm;
