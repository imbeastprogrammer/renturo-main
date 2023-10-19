import _ from 'lodash';
import { CSS } from '@dnd-kit/utilities';
import { GripVerticalIcon, TrashIcon } from 'lucide-react';
import { useSortable } from '@dnd-kit/sortable';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { FormFields } from '..';
import { Input } from '@/components/ui/input';
import { useController } from 'react-hook-form';
import { toolboxItems } from './toolboxItems';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type DropzoneItemProps = {
    index: number;
    item: FormFields;
    onRemove: () => void;
};

function DropzoneItem({ item, onRemove, index }: DropzoneItemProps) {
    const { attributes, listeners, setNodeRef, transform, transition } =
        useSortable({ id: item.id });

    const fieldTypes = _.flattenDeep(toolboxItems.map(({ items }) => items));

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
    };

    const labelField = useController({
        name: `custom_fields.${index}.label`,
    });
    const fieldTypeField = useController({
        name: `custom_fields.${index}.type`,
    });

    const SelectedFieldTypeIcon = fieldTypes.find(
        ({ id }) => id === fieldTypeField.field.value,
    )?.icon;

    return (
        <div
            ref={setNodeRef}
            style={style}
            className='grid grid-cols-[40px_1fr] gap-4 rounded-lg bg-white p-4'
        >
            <div>
                <Button
                    variant='ghost'
                    size='icon'
                    {...attributes}
                    {...listeners}
                >
                    <GripVerticalIcon className='text-blue-500' />
                </Button>
            </div>
            <div className='space-y-2'>
                <div className='flex items-center justify-between'>
                    <Select
                        value={fieldTypeField.field.value}
                        onValueChange={(value) =>
                            fieldTypeField.field.onChange(value)
                        }
                    >
                        <SelectTrigger className='w-[180px] border-none'>
                            <div className='flex items-center gap-2'>
                                {SelectedFieldTypeIcon && (
                                    <SelectedFieldTypeIcon className='text-metalic-blue' />
                                )}
                                <SelectValue />
                            </div>
                        </SelectTrigger>
                        <SelectContent>
                            {fieldTypes.map((fieldType, idx) => (
                                <SelectItem key={idx} value={fieldType.id}>
                                    {fieldType.title}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Button size='icon' variant='ghost' onClick={onRemove}>
                        <TrashIcon className='cursor-pointer text-red-500' />
                    </Button>
                </div>
                <Separator />
                <div>
                    <Input
                        value={labelField.field.value}
                        onChange={(e) =>
                            labelField.field.onChange(e.target.value)
                        }
                        className='border-none p-0 text-[20px] font-medium outline-none'
                    />
                </div>
                <div>
                    <Input />
                </div>
            </div>
        </div>
    );
}

export default DropzoneItem;
