import { GripVerticalIcon, TrashIcon } from 'lucide-react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { FormControl, FormItem, FormLabel } from '@/components/ui/form';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { FormFields } from '..';

type DropzoneItemProps = {
    item: FormFields[0];
    onRemove: () => void;
};

const TypeFieldTypeMap: Record<string, string> = {
    text: 'Text',
    textarea: 'TextArea',
    dropdown: 'Dropdown',
    checkbox: 'Checkbox',
    'checkbox-group': 'Checkbox Group',
    'radio-group': 'Radio Group',
    'file-upload': 'File Upload',
    number: 'Number',
    date: 'Date',
};

function DropzoneItem({ item, onRemove }: DropzoneItemProps) {
    return (
        <div className='grid grid-cols-[1fr_auto] gap-8 rounded-lg border-2 border-dashed bg-white p-4'>
            <div>
                <h1 className='text-[22px] font-semibold'>
                    {TypeFieldTypeMap[item.type]}
                </h1>
                <TextField label='Label' />
                <TextField label='Name' />
                <TextField label='Placeholder' />
                <TextField label='Min' />
                <TextField label='Max' />
                <FormItem className='flex items-center gap-4'>
                    <FormLabel className='min-w-[100px]'>Required</FormLabel>
                    <FormControl>
                        <RadioGroup defaultValue='yes' className='flex'>
                            <div className='flex items-center space-x-2'>
                                <RadioGroupItem
                                    className='text-metalic-blue'
                                    value='yes'
                                    id='yes'
                                />
                                <Label htmlFor='yes'>Yes</Label>
                            </div>
                            <div className='flex items-center space-x-2'>
                                <RadioGroupItem
                                    value='no'
                                    id='no'
                                    className='text-metalic-blue'
                                />
                                <Label htmlFor='no'>No</Label>
                            </div>
                        </RadioGroup>
                    </FormControl>
                </FormItem>
            </div>
            <div className='space-y-4'>
                <GripVerticalIcon className='text-blue-500' />
                <TrashIcon
                    className='cursor-pointer text-red-500'
                    onClick={onRemove}
                />
            </div>
        </div>
    );
}

type TextFieldProps = {
    label: string;
};

function TextField({ label }: TextFieldProps) {
    return (
        <FormItem className='flex items-center gap-4'>
            <FormLabel className='min-w-[100px]'>{label}</FormLabel>
            <FormControl>
                <Input className='textsm h-6 rounded-none bg-blue-50 focus-visible:ring-0 focus-visible:ring-offset-0' />
            </FormControl>
        </FormItem>
    );
}

export default DropzoneItem;
