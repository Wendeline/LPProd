import pysrt, spacy.cli


def lemmeWords(Splitmots):
    motLemme = []
    fr_content_dict = dict()

    content_vf = "\n".join(Splitmots)
    fr_pipeline = "fr_core_news_sm"
    dis = ["parser", "ner"]
    spacy.cli.download(fr_pipeline)
    fr_nlp = spacy.load(fr_pipeline, disable=dis)
    fr_content = fr_nlp(str(content_vf))
    for token in fr_content:
        t = token.lemma_.lower()
        if t in fr_content_dict.keys():
            fr_content_dict[t] += 1
        else:
            fr_content_dict[t] = 1

    for key, value in fr_content_dict.items():
        motLemme.append(key)

    return(motLemme)