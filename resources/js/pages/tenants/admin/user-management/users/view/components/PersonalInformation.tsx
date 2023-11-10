import * as z from 'zod';
import { router } from '@inertiajs/react';
import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Switch } from '@/components/ui/switch';
import { Form } from '@/components/ui/form';
import { Button } from '@/components/ui/button';
import FormInput from '@/components/forms/FormInput';
import FormSelect from '@/components/forms/FormSelect';
import { User } from '@/types/users';
import { useToast } from '@/components/ui/use-toast';
import _ from 'lodash';

const formSchema = z.object({
    first_name: z.string().nonempty(),
    last_name: z.string().nonempty(),
    email: z.string().nonempty(),
    phone_number: z.string().nonempty(),
    role: z.string().nonempty(),
});

type PersonalInformationFormFields = z.infer<typeof formSchema>;
const defaultValues: PersonalInformationFormFields = {
    first_name: '',
    last_name: '',
    phone_number: '',
    email: '',
    role: '',
};

type PersonalInformationProps = { user: User };
function PersonalIformation({ user }: PersonalInformationProps) {
    const [allowEditing, setAllowEditing] = useState(false);
    const { toast } = useToast();

    const form = useForm<PersonalInformationFormFields>({
        defaultValues,
        resolver: zodResolver(formSchema),
        values: {
            first_name: user.first_name,
            last_name: user.last_name,
            role: user.role,
            phone_number: user.verified_mobile_no.mobile_no,
            email: user.email,
        },
    });

    const onSubmit = form.handleSubmit(({ first_name, last_name, role }) => {
        router.put(
            `/admin/users/${user.id}`,
            { first_name, last_name, role },
            {
                onSuccess: () => {
                    toast({
                        title: 'Success',
                        description:
                            'The user has been successfully updated in the system.',
                        variant: 'default',
                    });
                    router.visit('/admin/users?active=Users', {
                        replace: true,
                    });
                },
                onError: (error) => {
                    toast({
                        title: 'Error',
                        description:
                            _.valuesIn(error)[0] ||
                            'Something went wrong, Please try again later.',
                        variant: 'destructive',
                    });
                },
            },
        );
    });

    return (
        <Form {...form}>
            <form onSubmit={onSubmit}>
                <div className='grid gap-6'>
                    <div className='space-y-4'>
                        <div className='flex items-center justify-between gap-4'>
                            <h1 className='text-[22px] text-heavy-carbon'>
                                Personal Information
                            </h1>
                            <label className='flex items-center gap-4 rounded-full bg-gray-200 p-2 px-4 font-semibold'>
                                <Switch
                                    checked={allowEditing}
                                    onCheckedChange={() =>
                                        setAllowEditing(!allowEditing)
                                    }
                                />
                                Allow Edit
                            </label>
                        </div>
                        <div className='grid grid-cols-2 gap-4'>
                            <FormInput
                                label='First Name'
                                name='first_name'
                                disabled={!allowEditing}
                                control={form.control}
                            />
                            <FormInput
                                label='Last Name'
                                name='last_name'
                                disabled={!allowEditing}
                                control={form.control}
                            />
                        </div>
                    </div>
                    <div className='space-y-4'>
                        <h1 className='text-[22px] text-heavy-carbon'>
                            Contact Information
                        </h1>
                        <div className='grid grid-cols-2 gap-4'>
                            <FormInput
                                label='Phone Number'
                                name='phone_number'
                                disabled
                                control={form.control}
                            />
                            <FormInput
                                label='Email Address'
                                name='email'
                                disabled
                                control={form.control}
                            />
                            <FormSelect
                                name='role'
                                label='Role'
                                data={[
                                    { label: 'ADMIN', value: 'ADMIN' },
                                    { label: 'OWNER', value: 'OWNER' },
                                    { label: 'USER', value: 'USER' },
                                ]}
                                control={form.control}
                            />
                        </div>
                    </div>
                    {allowEditing && (
                        <Button
                            type='submit'
                            className='ml-auto w-max bg-metalic-blue p-6 px-20 uppercase hover:bg-metalic-blue/90'
                        >
                            Update
                        </Button>
                    )}
                </div>
            </form>
        </Form>
    );
}

export default PersonalIformation;
