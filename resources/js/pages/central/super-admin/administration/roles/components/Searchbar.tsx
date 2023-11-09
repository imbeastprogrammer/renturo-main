import { SearchIcon } from 'lucide-react';
import { ComponentPropsWithoutRef } from 'react';

type SearchbarProps = ComponentPropsWithoutRef<'input'> & {
    onSearchClicked?: (value: string) => void;
};
function Searchbar({ onSearchClicked, ...props }: SearchbarProps) {
    return (
        <div className='flex h-[40px] w-[340px] gap-2 rounded-lg bg-yinmn-blue p-1'>
            <input
                type='text'
                className='flex-1 rounded-lg p-2 px-4 outline-none'
                placeholder='Search'
                {...props}
            />
            <button
                className='h-full w-[30px] text-white'
                onClick={() =>
                    onSearchClicked &&
                    typeof props.value === 'string' &&
                    onSearchClicked(props.value)
                }
            >
                <SearchIcon />
            </button>
        </div>
    );
}

export default Searchbar;
