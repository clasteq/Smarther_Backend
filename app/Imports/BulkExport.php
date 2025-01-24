<?php

namespace App\Imports;

use App\Models\User;
use App\Models\QuestionBanks;
use App\Models\QuestionBankItems;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;

class BulkExport implements FromArray, WithHeadings,FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */


    use Exportable;

    protected $checkedqb;

    public function __construct($checkedqb){

        $this->checkedqb = $checkedqb;
      
    }

    public function collection()
    {
        $exparray = [];  
        $this->checkedqb = explode(',', $this->checkedqb);
        QuestionBankItems::$admin = 1;
        $qb = QuestionBanks::leftjoin('terms', 'terms.id', 'question_banks.term_id')
                ->leftjoin('classes', 'classes.id', 'question_banks.class_id')
                ->leftjoin('subjects', 'subjects.id', 'question_banks.subject_id')
                ->leftjoin('chapters', 'chapters.id', 'question_banks.chapter_id')
                ->select('question_banks.*', 'classes.class_name', 'subjects.subject_name', 'chapters.chaptername', 'terms.term_name')
                ->whereIn('question_banks.id', $this->checkedqb)->get();

                if($qb->isNotEmpty()) {
                    $qb = $qb->toArray();  
    
                    foreach($qb as $arr) {
                        $data = ['qbid'=>$arr['id'], 'class_id'=>$arr['class_id'], 'class_name'=>$arr['class_name'],
                            'subject_id'=>$arr['subject_id'],  'subject_name'=>$arr['subject_name'], 
                            'chapter_id'=>$arr['chapter_id'], 'chapter_name'=>$arr['chaptername'], 
                            'term_id'=>$arr['term_id'], 'term_name'=>$arr['term_name']];
    
                        if(is_array($arr['questionbank_items']) && count($arr['questionbank_items'])>0) {
                            foreach($arr['questionbank_items'] as $item) {
    
                                if(is_array($item['qb_items']) && count($item['qb_items'])>0) {
                                    foreach($item['qb_items'] as $qbitem) {
    
                                        $data1 = ['question_type_id' =>$item['question_type_id'], 
                                            'question_type' =>$item['question_type'], 
                                            'question_bank_item_id' =>$qbitem->id, 
                                            'question' =>$qbitem->question, 'answer' =>$qbitem->answer, 
                                            'option_1' =>$qbitem->option_1, 'option_2' =>$qbitem->option_2, 
                                            'option_3' =>$qbitem->option_3, 'option_4' =>$qbitem->option_4, 
                                            'question_file' =>$qbitem->question_file ];
    
                                        $exparray[] = array_merge($data, $data1);
    
                                    }
                                }
                            } 
                        } 
                    } 
                }
                $collection = collect($exparray);
            //   echo "<pre>";  print_r($exparray);
            //     exit;
                return $collection;
    }

    
    public function array(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'qb_id',
            'class_id',
            'class_name',
            'subject_id',
            'subject_name',
            'chapter_id',
            'chapter_name',
            'term_id',
            'term_name',
            'question_type_id',
            'question_type',
            'question_bank_item_id',
            'question',
            'answer',
            'option_1',
            'option_2',
            'option_3',
            'option_4',
            'question_file'
        ];
    }



}